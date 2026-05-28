package com.mysticnusa.app.data.repository

import com.google.gson.Gson
import com.google.gson.JsonObject
import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.remote.RetrofitInstance

class NewsRepository {

    private val api = RetrofitInstance.api

    private fun parseErrorMessage(errorBody: String?, fallback: String): String {
        if (errorBody.isNullOrBlank()) return fallback
        return try {
            val json = Gson().fromJson(errorBody, JsonObject::class.java)
            json.get("message")?.asString ?: fallback
        } catch (e: Exception) {
            fallback
        }
    }

    suspend fun getNews(page: Int = 1): Result<PaginatedResponse<NewsItem>> {
        return try {
            val response = api.getNews(page)
            if (response.isSuccessful) {
                response.body()?.let {
                    Result.success(it)
                } ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get news: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
