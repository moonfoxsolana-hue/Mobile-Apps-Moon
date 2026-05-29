package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.repository.GamesRepository
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

enum class TriviaRoomPhase {
    NONE,
    ROOM_LIST,
    ROOM_LOBBY,
    ROOM_PLAYING,
    ROOM_RESULT
}

data class TriviaUiState(
    val isLoading: Boolean = false,
    val sessionId: Int? = null,
    val currentQuestion: TriviaQuestion? = null,
    val currentQuestionNumber: Int = 0,
    val totalQuestions: Int = 0,
    val score: Int = 0,
    val streak: Int = 0,
    val isComplete: Boolean = false,
    val finishResponse: TriviaFinishResponse? = null,
    val lastAnswerCorrect: Boolean? = null,
    val correctAnswer: String? = null,
    val showFeedback: Boolean = false,
    val error: String? = null,
    val statisticsData: TriviaStatisticsResponse? = null,
    val leaderboard: List<LeaderboardEntry> = emptyList(),
    val showStats: Boolean = false,
    val showLeaderboard: Boolean = false,
    val statsLoading: Boolean = false,
    val leaderboardLoading: Boolean = false,
    // Room multiplayer state
    val roomPhase: TriviaRoomPhase = TriviaRoomPhase.NONE,
    val rooms: List<TriviaRoomInfo> = emptyList(),
    val currentRoom: TriviaRoomInfo? = null,
    val playerId: Int? = null,
    val isHost: Boolean = false,
    val roomQuestion: TriviaQuestion? = null,
    val roomCurrentQuestion: Int = 0,
    val roomTotalQuestions: Int = 0,
    val roomScore: Int = 0,
    val roomComplete: Boolean = false,
    val roomLeaderboard: List<TriviaRoomLeaderboardEntry> = emptyList(),
    val roomFinished: Boolean = false,
    val roomLastAnswerCorrect: Boolean? = null,
    val roomCorrectAnswer: String? = null,
    val roomShowFeedback: Boolean = false,
    val roomLoading: Boolean = false,
    val roomLogicMode: Boolean = false
)

class TriviaViewModel(
    private val gamesRepository: GamesRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(TriviaUiState())
    val uiState: StateFlow<TriviaUiState> = _uiState.asStateFlow()

    private var pollingJob: Job? = null

    fun startGame(category: String? = null, questionCount: Int? = null) {
        viewModelScope.launch {
            _uiState.value = TriviaUiState(isLoading = true)
            try {
                val result = gamesRepository.startTrivia(TriviaStartRequest(category, questionCount))
                result.onSuccess { response ->
                    if (response.complete == true) {
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            sessionId = response.sessionId,
                            isComplete = true
                        )
                    } else {
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            sessionId = response.sessionId,
                            currentQuestion = response.question,
                            currentQuestionNumber = response.currentQuestion ?: 1,
                            totalQuestions = response.totalQuestion ?: 0
                        )
                    }
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        error = error.message ?: "Gagal memulai permainan"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun answerQuestion(questionId: Int, selectedAnswer: String) {
        val sessionId = _uiState.value.sessionId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, showFeedback = false)
            try {
                val result = gamesRepository.answerTrivia(
                    TriviaAnswerRequest(sessionId, questionId, selectedAnswer)
                )
                result.onSuccess { response ->
                    val isCorrect = response.isCorrect ?: false
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        lastAnswerCorrect = isCorrect,
                        correctAnswer = response.correctAnswer,
                        showFeedback = true,
                        streak = response.streak ?: _uiState.value.streak,
                        isComplete = response.complete ?: false
                    )
                    // Delay to show feedback, then advance
                    kotlinx.coroutines.delay(1500)
                    if (response.complete != true) {
                        _uiState.value = _uiState.value.copy(
                            currentQuestion = response.nextQuestion,
                            currentQuestionNumber = response.currentQuestion ?: _uiState.value.currentQuestionNumber + 1,
                            totalQuestions = response.totalQuestion ?: _uiState.value.totalQuestions,
                            showFeedback = false,
                            lastAnswerCorrect = null,
                            correctAnswer = null
                        )
                    }
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        error = error.message ?: "Gagal mengirim jawaban"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun finishGame() {
        val sessionId = _uiState.value.sessionId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            try {
                val result = gamesRepository.finishTrivia(TriviaFinishRequest(sessionId))
                result.onSuccess { response ->
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        finishResponse = response,
                        score = response.score ?: 0,
                        streak = response.streak ?: _uiState.value.streak,
                        isComplete = true
                    )
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        error = error.message ?: "Gagal menyelesaikan permainan"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun loadStatistics() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(statsLoading = true)
            try {
                val result = gamesRepository.getTriviaStatistics()
                result.onSuccess { response ->
                    _uiState.value = _uiState.value.copy(
                        statsLoading = false,
                        statisticsData = response
                    )
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        statsLoading = false,
                        error = error.message ?: "Gagal memuat statistik"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    statsLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun loadLeaderboard() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(leaderboardLoading = true)
            try {
                val result = gamesRepository.getTriviaLeaderboard()
                result.onSuccess { response ->
                    _uiState.value = _uiState.value.copy(
                        leaderboardLoading = false,
                        leaderboard = response
                    )
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        leaderboardLoading = false,
                        error = error.message ?: "Gagal memuat leaderboard"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    leaderboardLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun toggleStats() {
        val newShowStats = !_uiState.value.showStats
        _uiState.value = _uiState.value.copy(showStats = newShowStats, showLeaderboard = false)
        if (newShowStats && _uiState.value.statisticsData == null) {
            loadStatistics()
        }
    }

    fun toggleLeaderboard() {
        val newShowLeaderboard = !_uiState.value.showLeaderboard
        _uiState.value = _uiState.value.copy(showLeaderboard = newShowLeaderboard, showStats = false)
        if (newShowLeaderboard && _uiState.value.leaderboard.isEmpty()) {
            loadLeaderboard()
        }
    }

    fun clearError() {
        _uiState.value = _uiState.value.copy(error = null)
    }

    // ===== Room Multiplayer Functions =====

    fun enterRoomMode() {
        _uiState.value = _uiState.value.copy(roomPhase = TriviaRoomPhase.ROOM_LIST)
        loadRoomList()
    }

    fun exitRoomMode() {
        stopPolling()
        _uiState.value = _uiState.value.copy(
            roomPhase = TriviaRoomPhase.NONE,
            rooms = emptyList(),
            currentRoom = null,
            playerId = null,
            isHost = false,
            roomQuestion = null,
            roomCurrentQuestion = 0,
            roomTotalQuestions = 0,
            roomScore = 0,
            roomComplete = false,
            roomLeaderboard = emptyList(),
            roomFinished = false,
            roomLastAnswerCorrect = null,
            roomCorrectAnswer = null,
            roomShowFeedback = false,
            roomLoading = false,
            roomLogicMode = false
        )
    }

    fun loadRoomList() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(roomLoading = true)
            try {
                val result = gamesRepository.getTriviaRoomList()
                result.onSuccess { response ->
                    val pid = response.playerId
                    // If player has an active room, go to lobby
                    if (response.roomDetail != null) {
                        val room = response.roomDetail
                        _uiState.value = _uiState.value.copy(
                            roomLoading = false,
                            rooms = response.rooms ?: emptyList(),
                            currentRoom = room,
                            playerId = pid,
                            isHost = room.hostId == pid,
                            roomPhase = TriviaRoomPhase.ROOM_LOBBY
                        )
                        startLobbyPolling()
                    } else {
                        _uiState.value = _uiState.value.copy(
                            roomLoading = false,
                            rooms = response.rooms ?: emptyList(),
                            playerId = pid
                        )
                    }
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        error = error.message ?: "Gagal memuat daftar room"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    roomLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun createRoom(name: String, category: String, maxPlayers: Int, joinCode: String?, logicMode: Boolean?) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(roomLoading = true)
            try {
                val request = TriviaRoomCreateRequest(name, category, maxPlayers, joinCode, logicMode)
                val result = gamesRepository.createTriviaRoom(request)
                result.onSuccess { response ->
                    val pid = response.playerId
                    val room = response.roomDetail
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        currentRoom = room,
                        playerId = pid,
                        isHost = true,
                        roomPhase = TriviaRoomPhase.ROOM_LOBBY
                    )
                    startLobbyPolling()
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        error = error.message ?: "Gagal membuat room"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    roomLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun joinRoom(roomId: Int, joinCode: String?) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(roomLoading = true)
            try {
                val request = TriviaRoomJoinRequest(roomId, joinCode)
                val result = gamesRepository.joinTriviaRoom(request)
                result.onSuccess { response ->
                    val pid = response.playerId
                    val room = response.roomDetail
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        currentRoom = room,
                        playerId = pid,
                        isHost = room?.hostId == pid,
                        roomPhase = TriviaRoomPhase.ROOM_LOBBY
                    )
                    startLobbyPolling()
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        error = error.message ?: "Gagal bergabung room"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    roomLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun readyPlayer(isReady: Boolean) {
        val roomId = _uiState.value.currentRoom?.id ?: return
        viewModelScope.launch {
            try {
                val request = TriviaRoomReadyRequest(roomId, isReady)
                gamesRepository.readyTriviaRoom(request)
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(error = e.message ?: "Gagal mengubah status ready")
            }
        }
    }

    fun kickPlayer(targetPlayerId: Int) {
        val roomId = _uiState.value.currentRoom?.id ?: return
        viewModelScope.launch {
            try {
                val request = TriviaRoomKickRequest(roomId, targetPlayerId)
                gamesRepository.kickTriviaPlayer(request)
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(error = e.message ?: "Gagal mengeluarkan pemain")
            }
        }
    }

    fun startRoom(questionCount: Int? = null) {
        val roomId = _uiState.value.currentRoom?.id ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(roomLoading = true)
            try {
                val request = TriviaRoomStartRequest(roomId, questionCount)
                val result = gamesRepository.startTriviaRoom(request)
                result.onSuccess { response ->
                    stopPolling()
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        roomPhase = TriviaRoomPhase.ROOM_PLAYING,
                        roomQuestion = response.questions,
                        roomCurrentQuestion = response.currentQuestion ?: 1,
                        roomTotalQuestions = response.totalQuestions ?: 0,
                        roomLogicMode = response.logicMode ?: false
                    )
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        error = error.message ?: "Gagal memulai room"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    roomLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun answerRoomQuestion(questionId: Int, selectedAnswer: String) {
        val roomId = _uiState.value.currentRoom?.id ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(roomLoading = true, roomShowFeedback = false)
            try {
                val request = TriviaRoomAnswerRequest(roomId, questionId, selectedAnswer)
                val result = gamesRepository.answerTriviaRoom(request)
                result.onSuccess { response ->
                    val isCorrect = response.isCorrect ?: false
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        roomLastAnswerCorrect = isCorrect,
                        roomCorrectAnswer = response.correctAnswer,
                        roomShowFeedback = true,
                        roomScore = response.currentScore ?: _uiState.value.roomScore,
                        roomComplete = response.complete ?: false
                    )
                    delay(1500)
                    if (response.complete != true) {
                        _uiState.value = _uiState.value.copy(
                            roomQuestion = response.nextQuestion,
                            roomCurrentQuestion = response.currentQuestion ?: _uiState.value.roomCurrentQuestion + 1,
                            roomTotalQuestions = response.totalQuestion ?: _uiState.value.roomTotalQuestions,
                            roomShowFeedback = false,
                            roomLastAnswerCorrect = null,
                            roomCorrectAnswer = null
                        )
                    }
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        error = error.message ?: "Gagal mengirim jawaban"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    roomLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun finishRoom() {
        val roomId = _uiState.value.currentRoom?.id ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(roomLoading = true)
            try {
                val request = TriviaRoomFinishRequest(roomId)
                val result = gamesRepository.finishTriviaRoom(request)
                result.onSuccess { response ->
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        roomPhase = TriviaRoomPhase.ROOM_RESULT,
                        roomLeaderboard = response.leaderboard ?: emptyList(),
                        roomFinished = response.roomFinished ?: false
                    )
                    if (response.roomFinished != true) {
                        startResultPolling()
                    }
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        roomLoading = false,
                        error = error.message ?: "Gagal menyelesaikan room"
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    roomLoading = false,
                    error = e.message ?: "Terjadi kesalahan"
                )
            }
        }
    }

    fun exitRoom() {
        val roomId = _uiState.value.currentRoom?.id ?: return
        viewModelScope.launch {
            try {
                val request = TriviaRoomExitRequest(roomId)
                gamesRepository.exitTriviaRoom(request)
            } catch (_: Exception) { }
            stopPolling()
            _uiState.value = _uiState.value.copy(
                roomPhase = TriviaRoomPhase.ROOM_LIST,
                currentRoom = null,
                isHost = false,
                roomQuestion = null,
                roomCurrentQuestion = 0,
                roomTotalQuestions = 0,
                roomScore = 0,
                roomComplete = false,
                roomLeaderboard = emptyList(),
                roomFinished = false,
                roomLastAnswerCorrect = null,
                roomCorrectAnswer = null,
                roomShowFeedback = false
            )
            loadRoomList()
        }
    }

    private fun startLobbyPolling() {
        stopPolling()
        pollingJob = viewModelScope.launch {
            while (true) {
                delay(3000)
                try {
                    val result = gamesRepository.getTriviaActiveRoom()
                    result.onSuccess { response ->
                        when (response.state) {
                            "playing" -> {
                                stopPolling()
                                _uiState.value = _uiState.value.copy(
                                    roomPhase = TriviaRoomPhase.ROOM_PLAYING,
                                    currentRoom = response.roomDetail ?: _uiState.value.currentRoom,
                                    roomQuestion = response.question,
                                    roomCurrentQuestion = response.currentQuestion ?: 1,
                                    roomTotalQuestions = response.totalQuestion ?: 0,
                                    roomLogicMode = response.logicMode ?: false
                                )
                                return@launch
                            }
                            "finished" -> {
                                stopPolling()
                                _uiState.value = _uiState.value.copy(
                                    roomPhase = TriviaRoomPhase.ROOM_RESULT,
                                    currentRoom = response.roomDetail ?: _uiState.value.currentRoom
                                )
                                finishRoom()
                                return@launch
                            }
                            else -> {
                                // Still waiting - update room detail
                                response.roomDetail?.let { room ->
                                    _uiState.value = _uiState.value.copy(
                                        currentRoom = room,
                                        isHost = room.hostId == _uiState.value.playerId
                                    )
                                }
                            }
                        }
                    }
                } catch (_: Exception) { }
            }
        }
    }

    private fun startResultPolling() {
        stopPolling()
        pollingJob = viewModelScope.launch {
            while (true) {
                delay(3000)
                try {
                    val roomId = _uiState.value.currentRoom?.id ?: return@launch
                    val request = TriviaRoomFinishRequest(roomId)
                    val result = gamesRepository.finishTriviaRoom(request)
                    result.onSuccess { response ->
                        _uiState.value = _uiState.value.copy(
                            roomLeaderboard = response.leaderboard ?: _uiState.value.roomLeaderboard,
                            roomFinished = response.roomFinished ?: false
                        )
                        if (response.roomFinished == true) {
                            stopPolling()
                            return@launch
                        }
                    }
                } catch (_: Exception) { }
            }
        }
    }

    private fun stopPolling() {
        pollingJob?.cancel()
        pollingJob = null
    }

    override fun onCleared() {
        super.onCleared()
        stopPolling()
    }

    class Factory(
        private val gamesRepository: GamesRepository
    ) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            return TriviaViewModel(gamesRepository) as T
        }
    }
}
