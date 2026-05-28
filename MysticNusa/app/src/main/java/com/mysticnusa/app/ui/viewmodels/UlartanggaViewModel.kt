package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.mysticnusa.app.data.models.UlartanggaMatch
import com.mysticnusa.app.data.repository.GamesRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class UlartanggaUiState(
    val isLoading: Boolean = false,
    val matches: List<UlartanggaMatch> = emptyList(),
    val currentMatch: UlartanggaMatch? = null,
    val playerPosition: Int = 1,
    val lastDice: Int? = null,
    val lastEvent: String? = null,
    val error: String? = null
)

class UlartanggaViewModel(
    private val gamesRepository: GamesRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(UlartanggaUiState())
    val uiState: StateFlow<UlartanggaUiState> = _uiState.asStateFlow()

    fun loadMatches() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.getUlartanggaMatches()
            result.onSuccess { matches ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    matches = matches
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun createMatch() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.createUlartanggaMatch()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    currentMatch = response.match
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun joinMatch(matchId: Int) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.joinUlartanggaMatch(matchId)
            result.onSuccess {
                val match = _uiState.value.matches.find { it.id == matchId }
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    currentMatch = match
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun throwDice(matchId: Int) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.throwDice(matchId)
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    lastDice = response.dice,
                    playerPosition = response.position ?: _uiState.value.playerPosition,
                    lastEvent = response.event
                )
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
            return UlartanggaViewModel(gamesRepository) as T
        }
    }
}
