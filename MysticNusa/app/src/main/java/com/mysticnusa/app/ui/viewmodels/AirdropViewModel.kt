package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.mysticnusa.app.data.models.AirdropClaimResponse
import com.mysticnusa.app.data.repository.AirdropRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

data class AirdropUiState(
    val isLoading: Boolean = false,
    val claimResponse: AirdropClaimResponse? = null,
    val error: String? = null,
    val hasClaimed: Boolean = false,
    val notificationCounter: Int = 0
)

class AirdropViewModel(
    private val airdropRepository: AirdropRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(AirdropUiState())
    val uiState: StateFlow<AirdropUiState> = _uiState.asStateFlow()

    fun clearMessage() {
        _uiState.update { it.copy(claimResponse = null, notificationCounter = it.notificationCounter + 1) }
    }

    fun clearError() {
        _uiState.update { it.copy(error = null, notificationCounter = it.notificationCounter + 1) }
    }

    fun claimFirst(walletAddress: String) {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true, error = null) }
            val result = airdropRepository.claimFirst(walletAddress)
            result.onSuccess { response ->
                _uiState.update {
                    it.copy(
                        isLoading = false,
                        claimResponse = response,
                        hasClaimed = true,
                        notificationCounter = it.notificationCounter + 1
                    )
                }
            }.onFailure { error ->
                _uiState.update {
                    it.copy(
                        isLoading = false,
                        error = error.message,
                        notificationCounter = it.notificationCounter + 1
                    )
                }
            }
        }
    }

    fun claimWithCode(code: String) {
        viewModelScope.launch {
            _uiState.update { it.copy(isLoading = true, error = null) }
            val result = airdropRepository.claimWithCode(code)
            result.onSuccess { response ->
                _uiState.update {
                    it.copy(
                        isLoading = false,
                        claimResponse = response,
                        notificationCounter = it.notificationCounter + 1
                    )
                }
            }.onFailure { error ->
                _uiState.update {
                    it.copy(
                        isLoading = false,
                        error = error.message,
                        notificationCounter = it.notificationCounter + 1
                    )
                }
            }
        }
    }

    class Factory(
        private val airdropRepository: AirdropRepository
    ) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            return AirdropViewModel(airdropRepository) as T
        }
    }
}
