package com.mysticnusa.app.data.repository

import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.remote.RetrofitInstance

class StoryRepository {

    private val api = RetrofitInstance.api

    suspend fun getStories(page: Int = 1): Result<PaginatedResponse<StoryItem>> {
        return try {
            val response = api.getStories(page)
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get stories: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
