package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.repository.GamesRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class LogicalUiState(
    val isLoading: Boolean = false,
    val matchId: String? = null,
    val currentQuestion: LogicalQuestion? = null,
    val currentQuestionNumber: Int = 0,
    val totalQuestions: Int = 10,
    val isComplete: Boolean = false,
    val finishResponse: LogicalFinishResponse? = null,
    val error: String? = null,
    val statisticsData: LogicalStatisticsResponse? = null,
    val leaderboard: List<LeaderboardEntry> = emptyList(),
    val showStats: Boolean = false,
    val showLeaderboard: Boolean = false,
    val statsLoading: Boolean = false,
    val leaderboardLoading: Boolean = false
)

class LogicalViewModel(
    private val gamesRepository: GamesRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(LogicalUiState())
    val uiState: StateFlow<LogicalUiState> = _uiState.asStateFlow()

    fun startGame() {
        viewModelScope.launch {
            _uiState.value = LogicalUiState(isLoading = true)
            try {
                val result = gamesRepository.startLogical()
                result.onSuccess { response ->
                    if (response.complete == true) {
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            matchId = response.matchId,
                            isComplete = true
                        )
                    } else {
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            matchId = response.matchId,
                            currentQuestion = response.question,
                            currentQuestionNumber = response.currentQuestion ?: 1,
                            totalQuestions = response.totalQuestion ?: 10
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

    fun answerQuestion(questionId: String, answerId: String) {
        val matchId = _uiState.value.matchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            try {
                val result = gamesRepository.answerLogical(
                    LogicalAnswerRequest(matchId, questionId, answerId)
                )
                result.onSuccess { response ->
                    if (response.complete == true) {
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            currentQuestion = null,
                            isComplete = true
                        )
                    } else {
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            currentQuestion = response.nextQuestion,
                            currentQuestionNumber = response.currentQuestion ?: _uiState.value.currentQuestionNumber + 1,
                            totalQuestions = response.totalQuestion ?: _uiState.value.totalQuestions
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
        val matchId = _uiState.value.matchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            try {
                val result = gamesRepository.finishLogical(matchId)
                result.onSuccess { response ->
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        finishResponse = response,
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
                val result = gamesRepository.getLogicalStatistics()
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
                val result = gamesRepository.getLogicalLeaderboard()
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

    class Factory(
        private val gamesRepository: GamesRepository
    ) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            return LogicalViewModel(gamesRepository) as T
        }
    }
}
