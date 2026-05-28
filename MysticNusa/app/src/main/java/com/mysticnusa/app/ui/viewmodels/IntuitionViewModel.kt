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
    val totalRounds: Int = 0,
    val items: List<IntuitionRoundItem> = emptyList(),
    val score: Int = 0,
    val isComplete: Boolean = false,
    val error: String? = null
)

class IntuitionViewModel(
    private val gamesRepository: GamesRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(IntuitionUiState())
    val uiState: StateFlow<IntuitionUiState> = _uiState.asStateFlow()

    fun startGame() {
        viewModelScope.launch {
            _uiState.value = IntuitionUiState(isLoading = true)
            val result = gamesRepository.startIntuition()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    matchId = response.matchId,
                    currentRound = response.currentRound ?: 1,
                    totalRounds = response.totalRounds ?: 0
                )
                response.matchId?.let { loadRoundItems(it) }
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    private fun loadRoundItems(matchId: String) {
        viewModelScope.launch {
            val result = gamesRepository.getIntuitionRoundItems(matchId)
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    items = response.items ?: emptyList()
                )
            }
        }
    }

    fun answerRound(chosenItemId: Int) {
        val matchId = _uiState.value.matchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            val result = gamesRepository.answerIntuition(
                matchId, IntuitionAnswerRequest(chosenItemId)
            )
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    currentRound = response.currentRound ?: _uiState.value.currentRound,
                    score = response.score ?: _uiState.value.score,
                    isComplete = response.complete ?: false
                )
                if (response.complete != true) {
                    loadRoundItems(matchId)
                }
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
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
