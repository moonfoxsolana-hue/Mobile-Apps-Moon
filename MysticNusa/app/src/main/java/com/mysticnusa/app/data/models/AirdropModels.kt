package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

data class AirdropClaimResponse(
    val status: String?,
    val message: String?,
    @SerializedName("tokens_received")
    val tokensReceived: Double?
)

data class AirdropCodeRequest(
    val code: String
)
