package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.mysticnusa.app.data.local.TokenManager
import com.mysticnusa.app.data.models.AuthResponse
import com.mysticnusa.app.data.models.LoginRequest
import com.mysticnusa.app.data.models.RegisterRequest
import com.mysticnusa.app.data.repository.AuthRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class AuthUiState(
    val isLoading: Boolean = false,
    val authResponse: AuthResponse? = null,
    val error: String? = null,
    val isLoggedIn: Boolean = false
)

class AuthViewModel(
    private val authRepository: AuthRepository,
    private val tokenManager: TokenManager
) : ViewModel() {

    private val _uiState = MutableStateFlow(AuthUiState())
    val uiState: StateFlow<AuthUiState> = _uiState.asStateFlow()

    fun login(email: String, password: String) {
        viewModelScope.launch {
            try {
                _uiState.value = _uiState.value.copy(isLoading = true, error = null)
                val result = authRepository.login(LoginRequest(email, password))
                result.onSuccess { response ->
                    response.accessToken?.let {
                        try {
                            tokenManager.saveToken(it)
                        } catch (e: Exception) {
                            // Token save failed, but login still succeeded
                        }
                    }
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        authResponse = response,
                        isLoggedIn = true
                    )
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        error = error.message ?: "Login gagal"
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

    fun register(name: String, email: String, password: String, passwordConfirmation: String) {
        viewModelScope.launch {
            try {
                _uiState.value = _uiState.value.copy(isLoading = true, error = null)
                val result = authRepository.register(
                    RegisterRequest(name, email, password, passwordConfirmation)
                )
                result.onSuccess { response ->
                    response.accessToken?.let {
                        try {
                            tokenManager.saveToken(it)
                        } catch (e: Exception) {
                            // Token save failed
                        }
                    }
                    try {
                        tokenManager.saveUserName(name)
                    } catch (e: Exception) {
                        // Username save failed
                    }
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        authResponse = response,
                        isLoggedIn = true
                    )
                }.onFailure { error ->
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        error = error.message ?: "Registrasi gagal"
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

    fun logout() {
        viewModelScope.launch {
            try {
                authRepository.logout()
            } catch (e: Exception) {
                // Ignore logout API failure
            }
            try {
                tokenManager.clearAll()
            } catch (e: Exception) {
                // Ignore clear failure
            }
            _uiState.value = AuthUiState()
        }
    }

    class Factory(
        private val authRepository: AuthRepository,
        private val tokenManager: TokenManager
    ) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            return AuthViewModel(authRepository, tokenManager) as T
        }
    }
}
