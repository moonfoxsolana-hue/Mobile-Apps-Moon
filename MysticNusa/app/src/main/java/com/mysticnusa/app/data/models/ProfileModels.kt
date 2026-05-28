package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

data class UserProfile(
    val name: String?,
    val email: String?,
    @SerializedName("wallet_address")
    val walletAddress: String?,
    @SerializedName("total_token")
    val totalToken: Double?,
    @SerializedName("has_claimed")
    val hasClaimed: Boolean?,
    @SerializedName("locked_balance")
    val lockedBalance: Double?
)

data class TokenHistoryItem(
    val id: Int,
    val type: String?,
    val amount: Double?,
    val action: String?,
    val description: String?,
    @SerializedName("created_at")
    val createdAt: String?
)

data class ProfileResponse(
    val status: String?,
    val user: UserProfile?
)

data class TokenHistoryResponse(
    val status: String?,
    val history: List<TokenHistoryItem>?
)
