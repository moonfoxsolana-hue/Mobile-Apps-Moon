package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.mysticnusa.app.data.models.StoryItem
import com.mysticnusa.app.data.repository.StoryRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class StoryUiState(
    val isLoading: Boolean = false,
    val stories: List<StoryItem> = emptyList(),
    val currentPage: Int = 1,
    val lastPage: Int = 1,
    val error: String? = null
)

class StoryViewModel(
    private val storyRepository: StoryRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(StoryUiState())
    val uiState: StateFlow<StoryUiState> = _uiState.asStateFlow()

    fun loadStories(page: Int = 1) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = storyRepository.getStories(page)
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    stories = if (page == 1) response.data else _uiState.value.stories + response.data,
                    currentPage = response.currentPage,
                    lastPage = response.lastPage
                )
            }.onFailure { error ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = error.message
                )
            }
        }
    }

    fun loadMore() {
        val currentState = _uiState.value
        if (currentState.currentPage < currentState.lastPage && !currentState.isLoading) {
            loadStories(currentState.currentPage + 1)
        }
    }

    class Factory(
        private val storyRepository: StoryRepository
    ) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            return StoryViewModel(storyRepository) as T
        }
    }
}
