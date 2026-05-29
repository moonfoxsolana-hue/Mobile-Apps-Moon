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

data class IntuitionUiState(
    val isLoading: Boolean = false,
    val matchId: String? = null,
    val currentRound: Int = 0,
    val totalRounds: Int = 10,
    val items: List<IntuitionRoundItem> = emptyList(),
    val score: Int = 0,
    val isComplete: Boolean = false,
    val lastAnswerCorrect: Boolean? = null,
    val showFeedback: Boolean = false,
    val error: String? = null,
    val statisticsData: IntuitionStatisticsResponse? = null,
    val leaderboard: List<LeaderboardEntry> = emptyList(),
    val showStats: Boolean = false,
    val showLeaderboard: Boolean = false,
    val statsLoading: Boolean = false,
    val leaderboardLoading: Boolean = false
)

class IntuitionViewModel(
    private val gamesRepository: GamesRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(IntuitionUiState())
    val uiState: StateFlow<IntuitionUiState> = _uiState.asStateFlow()

    fun startGame() {
        viewModelScope.launch {
            _uiState.value = IntuitionUiState(isLoading = true)
            try {
                val result = gamesRepository.startIntuition()
                result.onSuccess { response ->
                    val matchId = response.matchId
                    val currentRound = response.currentRound ?: 0
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        matchId = matchId,
                        currentRound = currentRound + 1,
                        totalRounds = response.totalRounds ?: 10,
                        error = null
                    )
                    matchId?.let { loadRoundItems(it) }
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

    private fun loadRoundItems(matchId: String) {
        viewModelScope.launch {
            try {
                val result = gamesRepository.getIntuitionRoundItems(matchId)
                result.onSuccess { response ->
                    _uiState.value = _uiState.value.copy(
                        items = response.options ?: emptyList(),
                        currentRound = response.round ?: _uiState.value.currentRound,
                        showFeedback = false,
                        lastAnswerCorrect = null
                    )
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        isComplete = true,
                        error = null
                    )
                }
            } catch (e: Exception) {
                _uiState.value = _uiState.value.copy(
                    error = e.message ?: "Gagal memuat item"
                )
            }
        }
    }

    fun answerRound(chosenItemId: String) {
        val matchId = _uiState.value.matchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            try {
                val result = gamesRepository.answerIntuition(
                    matchId, IntuitionAnswerRequest(chosenItemId)
                )
                result.onSuccess { response ->
                    val isCorrect = response.correct ?: false
                    val newScore = if (isCorrect) _uiState.value.score + 1 else _uiState.value.score
                    val isComplete = response.matchCompleted ?: false

                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        score = newScore,
                        lastAnswerCorrect = isCorrect,
                        showFeedback = true,
                        isComplete = isComplete
                    )

                    if (!isComplete) {
                        kotlinx.coroutines.delay(1200)
                        loadRoundItems(matchId)
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

    fun loadStatistics() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(statsLoading = true)
            try {
                val result = gamesRepository.getIntuitionStatistics()
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
                val result = gamesRepository.getIntuitionLeaderboard()
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
            return IntuitionViewModel(gamesRepository) as T
        }
    }
}
