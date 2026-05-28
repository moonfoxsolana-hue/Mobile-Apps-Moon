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

data class TarotUiState(
    val isLoading: Boolean = false,
    val sessionId: String? = null,
    val cards: List<TarotCardOption> = emptyList(),
    val reading: String? = null,
    val cardDetails: List<TarotCardDetail> = emptyList(),
    val error: String? = null
)

class TarotViewModel(
    private val gamesRepository: GamesRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(TarotUiState())
    val uiState: StateFlow<TarotUiState> = _uiState.asStateFlow()

    fun startRitual() {
        viewModelScope.launch {
            _uiState.value = TarotUiState(isLoading = true)
            val result = gamesRepository.startTarot()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    sessionId = response.sessionId,
                    cards = response.cards ?: emptyList()
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun pickCards(name: String?, energyChoice: String?, cardIds: List<Int>) {
        val sessionId = _uiState.value.sessionId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            val result = gamesRepository.pickTarotCards(
                TarotPickRequest(sessionId, name, energyChoice, cardIds)
            )
            result.onSuccess {
                _uiState.value = _uiState.value.copy(isLoading = false)
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun getReading(oracleName: String?) {
        val sessionId = _uiState.value.sessionId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            val result = gamesRepository.getTarotReading(
                TarotReadingRequest(sessionId, oracleName)
            )
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    reading = response.reading,
                    cardDetails = response.cards ?: emptyList()
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
            return TarotViewModel(gamesRepository) as T
        }
    }
}
