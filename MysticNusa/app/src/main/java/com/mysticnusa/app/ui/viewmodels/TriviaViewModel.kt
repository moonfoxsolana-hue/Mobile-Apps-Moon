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
    val error: String? = null
)

class TriviaViewModel(
    private val gamesRepository: GamesRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(TriviaUiState())
    val uiState: StateFlow<TriviaUiState> = _uiState.asStateFlow()

    fun startGame(category: String? = null, questionCount: Int? = null) {
        viewModelScope.launch {
            _uiState.value = TriviaUiState(isLoading = true)
            val result = gamesRepository.startTrivia(TriviaStartRequest(category, questionCount))
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    sessionId = response.sessionId,
                    currentQuestion = response.question,
                    currentQuestionNumber = response.currentQuestion ?: 1,
                    totalQuestions = response.totalQuestion ?: 0
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun answerQuestion(questionId: Int, selectedAnswer: String) {
        val sessionId = _uiState.value.sessionId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            val result = gamesRepository.answerTrivia(
                TriviaAnswerRequest(sessionId, questionId, selectedAnswer)
            )
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    currentQuestion = response.nextQuestion,
                    currentQuestionNumber = response.currentQuestion ?: _uiState.value.currentQuestionNumber,
                    streak = response.streak ?: _uiState.value.streak,
                    isComplete = response.complete ?: false
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun finishGame() {
        val sessionId = _uiState.value.sessionId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true)
            val result = gamesRepository.finishTrivia(TriviaFinishRequest(sessionId))
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    finishResponse = response,
                    score = response.score ?: 0,
                    isComplete = true
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
            return TriviaViewModel(gamesRepository) as T
        }
    }
}
