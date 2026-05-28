package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.mysticnusa.app.data.models.StakeRequest
import com.mysticnusa.app.data.models.StakingType
import com.mysticnusa.app.data.models.UserStaking
import com.mysticnusa.app.data.repository.StakingRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class StakingUiState(
    val isLoading: Boolean = false,
    val stakingTypes: List<StakingType> = emptyList(),
    val userStakings: List<UserStaking> = emptyList(),
    val error: String? = null,
    val message: String? = null
)

class StakingViewModel(
    private val stakingRepository: StakingRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(StakingUiState())
    val uiState: StateFlow<StakingUiState> = _uiState.asStateFlow()

    fun loadStakingTypes() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = stakingRepository.getTypes()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    stakingTypes = response
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun loadUserStakings() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = stakingRepository.getUserStakings()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    userStakings = response
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun stake(typeId: Int, durationId: Int) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = stakingRepository.stake(StakeRequest(typeId, durationId))
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = response.message
                )
                loadUserStakings()
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun claimReward(id: Int) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = stakingRepository.claimReward(id)
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = response.message
                )
                loadUserStakings()
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun cancelStaking(id: Int) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = stakingRepository.cancelStaking(id)
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = response.message
                )
                loadUserStakings()
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    class Factory(
        private val stakingRepository: StakingRepository
    ) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            return StakingViewModel(stakingRepository) as T
        }
    }
}
