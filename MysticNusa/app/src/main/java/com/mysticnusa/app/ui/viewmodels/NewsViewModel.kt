package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.mysticnusa.app.data.models.NewsItem
import com.mysticnusa.app.data.repository.NewsRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class NewsUiState(
    val isLoading: Boolean = false,
    val news: List<NewsItem> = emptyList(),
    val currentPage: Int = 1,
    val lastPage: Int = 1,
    val error: String? = null
)

class NewsViewModel(
    private val newsRepository: NewsRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(NewsUiState())
    val uiState: StateFlow<NewsUiState> = _uiState.asStateFlow()

    fun loadNews(page: Int = 1) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = newsRepository.getNews(page)
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    news = if (page == 1) response.data else _uiState.value.news + response.data,
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
            loadNews(currentState.currentPage + 1)
        }
    }

    class Factory(
        private val newsRepository: NewsRepository
    ) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            return NewsViewModel(newsRepository) as T
        }
    }
}
