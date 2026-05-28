package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

data class StoryItem(
    val id: Int,
    val date: String?,
    val title: String?,
    val theme: String?,
    val content: String?,
    @SerializedName("audio_path")
    val audioPath: String?
)
