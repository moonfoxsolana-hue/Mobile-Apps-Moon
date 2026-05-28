package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

data class NewsItem(
    val id: Int,
    val title: String?,
    val content: String?,
    val image: String?,
    @SerializedName("created_at")
    val createdAt: String?
)

data class PaginatedResponse<T>(
    @SerializedName("current_page")
    val currentPage: Int,
    val data: List<T>,
    @SerializedName("last_page")
    val lastPage: Int,
    @SerializedName("per_page")
    val perPage: Int,
    val total: Int
)
