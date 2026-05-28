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
    val lastAnswerCorrect: Boolean? = null,
    val correctAnswer: String? = null,
    val showFeedback: Boolean = false,
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

    fun clearError() {
        _uiState.value = _uiState.value.copy(error = null)
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
