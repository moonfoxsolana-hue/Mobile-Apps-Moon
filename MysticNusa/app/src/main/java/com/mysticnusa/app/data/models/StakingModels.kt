package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

data class StakingType(
    val id: Int,
    val name: String?,
    @SerializedName("amount_token")
    val amountToken: Double?,
    val apr: Double?,
    val durations: List<StakingDuration>?
)

data class StakingDuration(
    val id: Int,
    val days: Int?,
    val apr: Double?
)

data class UserStaking(
    val id: Int,
    val amount: Double?,
    @SerializedName("expected_reward")
    val expectedReward: Double?,
    @SerializedName("start_date")
    val startDate: String?,
    @SerializedName("end_date")
    val endDate: String?,
    val status: String?,
    val claimed: Boolean?
)

data class StakeRequest(
    @SerializedName("type_id")
    val typeId: Int,
    @SerializedName("duration_id")
    val durationId: Int
)

data class StakingTypesResponse(
    val status: String?,
    val types: List<StakingType>?
)

data class UserStakingsResponse(
    val status: String?,
    val stakings: List<UserStaking>?
)
