package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

// === Match List (GET /ngepet/match) ===
data class NgepetMatchListItem(
    val id: String,
    @SerializedName("host_name") val hostName: String?,
    @SerializedName("house_avatar_id") val houseAvatarId: Int?,
    @SerializedName("token_pool") val tokenPool: Int,
    @SerializedName("total_token_pool") val totalTokenPool: Int?,
    @SerializedName("min_intruder_token") val minIntruderToken: Int?,
    @SerializedName("max_intruder_token") val maxIntruderToken: Int?,
    val difficulty: String?,
    @SerializedName("guess_duration_hours") val guessDurationHours: Int?,
    @SerializedName("max_intruders") val maxIntruders: Int?,
    val status: String?,
    @SerializedName("intruders_count") val intrudersCount: Int?,
    @SerializedName("house_avatar") val houseAvatar: NgepetAvatarInfo?
)

data class NgepetMatchesResponse(
    val status: String?,
    val matches: List<NgepetMatchListItem>?
)

// === Match Detail (GET /ngepet/match/{id}) ===
data class NgepetMatchDetail(
    val id: String?,
    @SerializedName("host_name") val hostName: String?,
    @SerializedName("token_pool") val tokenPool: Int?,
    @SerializedName("total_token_pool") val totalTokenPool: Int?,
    val difficulty: String?,
    @SerializedName("guess_duration_hours") val guessDurationHours: Int?,
    @SerializedName("max_intruders") val maxIntruders: Int?,
    val status: String?,
    @SerializedName("house_avatar") val houseAvatar: NgepetAvatarInfo?,
    val items: List<NgepetItem>?,
    val intruders: List<NgepetIntruder>?,
    @SerializedName("intruders_count") val intrudersCount: Int?,
    @SerializedName("hidden_tokens_count") val hiddenTokensCount: Int?,
    val events: List<NgepetEvent>?,
    @SerializedName("hidden_items") val hiddenItems: List<NgepetHiddenItem>?
)

data class NgepetMatchDetailResponse(
    val match: NgepetMatchDetail?
)

// === Nested models ===
data class NgepetAvatarInfo(
    val id: Int?,
    val name: String?,
    @SerializedName("image_url") val imageUrl: String?,
    val tier: String?
)

data class NgepetItem(
    val name: String?,
    @SerializedName("image_url") val imageUrl: String?
)

data class NgepetIntruder(
    val id: String?,
    @SerializedName("intruder_name") val intruderName: String?,
    @SerializedName("avatar_id") val avatarId: String?,
    val status: String?,
    @SerializedName("intruders_at") val intrudersAt: String?,
    @SerializedName("guess_deadline") val guessDeadline: String?,
    @SerializedName("is_pick_choice") val isPickChoice: Int?,
    val result: String?,
    @SerializedName("token_pool") val tokenPool: Int?,
    val avatar: NgepetAvatarInfo?
)

data class NgepetEvent(
    val role: String?,
    val details: String?,
    @SerializedName("created_at") val createdAt: String?
)

data class NgepetHiddenItem(
    val id: String?,
    val status: String?,
    val result: String?
)

// === Active Match (GET /ngepet/match/active) ===
data class NgepetActiveMatchResponse(
    val status: String?,
    val data: NgepetActiveMatchData?,
    val token: String?
)

data class NgepetActiveMatchData(
    @SerializedName("match_id") val matchId: String?,
    val role: String?,
    val status: String?,
    @SerializedName("token_pool") val tokenPool: Int?,
    @SerializedName("host_name") val hostName: String?,
    @SerializedName("intruders_count") val intrudersCount: Int?,
    @SerializedName("max_intruders") val maxIntruders: Int?,
    @SerializedName("intruder_match_id") val intruderMatchId: String?
)

// === Create Match (POST /ngepet/match/create) ===
data class NgepetCreateRequest(
    @SerializedName("host_name") val hostName: String,
    val difficulty: String,
    @SerializedName("guess_duration_hours") val guessDurationHours: Int,
    @SerializedName("max_intruders") val maxIntruders: Int,
    @SerializedName("token_pool") val tokenPool: Int,
    @SerializedName("min_intruder_token") val minIntruderToken: Int? = null,
    @SerializedName("max_intruder_token") val maxIntruderToken: Int? = null,
    @SerializedName("house_avatar_id") val houseAvatarId: Int? = null
)

data class NgepetCreateResponse(
    val status: String?,
    val message: String?,
    val id: String?
)

// === Join Match (POST /ngepet/match/{id}/join) ===
data class NgepetJoinRequest(
    val name: String,
    @SerializedName("token_amount") val tokenAmount: Int,
    @SerializedName("avatar_id") val avatarId: Int? = null
)

// === Submit Choice (POST /ngepet/match/{id}/submit-choice) ===
data class NgepetSubmitChoiceRequest(
    @SerializedName("item_name") val itemName: String
)

// === Host Guess (POST /ngepet/match/{id}/guess) ===
data class NgepetHostGuessRequest(
    @SerializedName("match_intruder_id") val matchIntruderId: String,
    @SerializedName("item_name") val itemName: String
)

// === Guess Response (used by both host guess and hidden guess) ===
data class NgepetGuessResponse(
    val status: String?,
    @SerializedName("is_correct") val isCorrect: Boolean?,
    @SerializedName("is_end") val isEnd: Boolean?,
    @SerializedName("answer_item") val answerItem: String?
)

// === Hidden Item (POST /ngepet/match/{id}/hidden-item) ===
data class NgepetHiddenItemRequest(
    @SerializedName("item_name") val itemName: String
)

// === Hidden Guess (POST /ngepet/match/{id}/hidden-guess) ===
data class NgepetHiddenGuessRequest(
    @SerializedName("hidden_item_id") val hiddenItemId: String,
    @SerializedName("match_intruder_id") val matchIntruderId: String,
    @SerializedName("item_name") val itemName: String
)

// === Claim Victory (POST /ngepet/match/claim-victory) ===
data class NgepetClaimVictoryRequest(
    @SerializedName("match_intruder_id") val matchIntruderId: String
)

// === Close Match (POST /ngepet/match/{id}/close) ===
data class NgepetCloseResponse(
    val status: String?,
    val message: String?
)

// === Avatar Shop (GET /ngepet/avatar) ===
data class NgepetAvatarShopItem(
    val id: Int,
    val name: String?,
    @SerializedName("image_url") val imageUrl: String?,
    val price: Int?,
    val stock: Int?,
    val tier: String?,
    val own: Int?
)

data class NgepetAvatarShopResponse(
    val status: String?,
    val data: List<NgepetAvatarShopItem>?
)

// === Owned Avatars (GET /ngepet/avatar/own) ===
data class NgepetOwnedAvatar(
    val id: Int,
    @SerializedName("avatar_id") val avatarId: Int?,
    @SerializedName("is_equipped") val isEquipped: Int?,
    val avatar: NgepetAvatarDetail?
)

data class NgepetAvatarDetail(
    val id: Int?,
    val name: String?,
    @SerializedName("image_url") val imageUrl: String?,
    val tier: String?,
    val type: String?
)

data class NgepetOwnedAvatarsResponse(
    val status: String?,
    val data: List<NgepetOwnedAvatar>?
)

// === History (GET /ngepet/match/history) ===
data class NgepetHistoryItem(
    @SerializedName("match_id") val matchId: String?,
    @SerializedName("host_name") val hostName: String?,
    val status: String?,
    val role: String?,
    @SerializedName("match_result") val matchResult: String?,
    @SerializedName("created_at") val createdAt: String?
)

data class NgepetHistoryResponse(
    val status: String?,
    val data: List<NgepetHistoryItem>?
)

// === Leaderboards ===
data class NgepetLeaderboardHouseItem(
    @SerializedName("match_id") val matchId: String?,
    @SerializedName("host_name") val hostName: String?,
    @SerializedName("avatar_id") val avatarId: Int?,
    val avatar: NgepetAvatarInfo?,
    @SerializedName("token_pool") val tokenPool: Int?
)

data class NgepetLeaderboardHostItem(
    @SerializedName("host_user_id") val hostUserId: Int?,
    @SerializedName("host_name") val hostName: String?,
    @SerializedName("total_intruder_games") val totalIntruderGames: Int?,
    @SerializedName("total_wins") val totalWins: Int?,
    @SerializedName("winrate_percentage") val winratePercentage: Double?
)

data class NgepetLeaderboardIntruderItem(
    @SerializedName("intruder_name") val intruderName: String?,
    @SerializedName("total_games") val totalGames: Int?,
    @SerializedName("total_wins") val totalWins: Int?,
    @SerializedName("win_rate") val winRate: Double?
)

data class NgepetLeaderboardResponse(
    val status: String?,
    val data: List<Any>?
)
