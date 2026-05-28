package com.mysticnusa.app.data.repository

import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.remote.RetrofitInstance

class NewsRepository {

    private val api = RetrofitInstance.api

    suspend fun getNews(page: Int = 1): Result<PaginatedResponse<NewsItem>> {
        return try {
            val response = api.getNews(page)
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get news: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
