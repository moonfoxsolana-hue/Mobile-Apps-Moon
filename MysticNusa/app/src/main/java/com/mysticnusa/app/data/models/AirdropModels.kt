package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

data class AirdropClaimResponse(
    val message: String?,
    val amount: Int?
)

data class AirdropClaimRequest(
    @SerializedName("wallet_address")
    val walletAddress: String
)

data class AirdropCodeRequest(
    val code: String
)
