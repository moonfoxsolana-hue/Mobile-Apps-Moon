package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

// Common
data class ApiResponse(
    val status: String?,
    val message: String?,
    val success: String? = null
)

data class LeaderboardEntry(
    val name: String?,
    @SerializedName("highest_score")
    val highestScore: Int? = null,
    @SerializedName("highest_iq")
    val highestIq: Int? = null,
    val score: Int? = null,
    val rank: Int? = null,
    @SerializedName("total_played")
    val totalPlayed: Int? = null,
    @SerializedName("total_correct")
    val totalCorrect: Int? = null,
    val level: Int? = null
)



// Trivia
data class TriviaStartRequest(
    val category: String?,
    @SerializedName("question_count")
    val questionCount: Int?
)

data class TriviaQuestion(
    val id: Int,
    val question: String?,
    val answers: List<String>?,
    @SerializedName("correct_answer")
    val correctAnswer: String?,
    val category: String?
)

data class TriviaStartResponse(
    val status: String?,
    @SerializedName("session_id")
    val sessionId: Int?,
    val question: TriviaQuestion?,
    @SerializedName("current_question")
    val currentQuestion: Int?,
    @SerializedName("total_question")
    val totalQuestion: Int?,
    val complete: Boolean?,
    val message: String?
)

data class TriviaAnswerRequest(
    @SerializedName("session_id")
    val sessionId: Int,
    @SerializedName("question_id")
    val questionId: Int,
    @SerializedName("selected_answer")
    val selectedAnswer: String
)

data class TriviaAnswerResponse(
    val status: String?,
    @SerializedName("is_correct")
    val isCorrect: Boolean?,
    @SerializedName("correct_answer")
    val correctAnswer: String?,
    val streak: Int?,
    @SerializedName("next_question")
    val nextQuestion: TriviaQuestion?,
    @SerializedName("current_question")
    val currentQuestion: Int?,
    @SerializedName("total_question")
    val totalQuestion: Int?,
    val complete: Boolean?
)

data class TriviaFinishRequest(
    @SerializedName("session_id")
    val sessionId: Int
)

data class TriviaFinishResponse(
    val status: String?,
    val score: Int?,
    val streak: Int?,
    @SerializedName("duration_seconds")
    val durationSeconds: Int?,
    val category: String? = null
)

// Logical
data class LogicalStartResponse(
    val status: String?,
    @SerializedName("match_id")
    val matchId: String?,
    val question: LogicalQuestion?,
    @SerializedName("current_question")
    val currentQuestion: Int?,
    @SerializedName("total_question")
    val totalQuestion: Int?,
    val complete: Boolean?,
    val message: String?
)

data class LogicalQuestion(
    val id: String,
    val question: String?,
    val answers: List<LogicalAnswer>?
)

data class LogicalAnswer(
    val id: String,
    val text: String?,
    val value: Int?,
    @SerializedName("question_id")
    val questionId: String? = null
)

data class LogicalAnswerRequest(
    @SerializedName("match_id")
    val matchId: String,
    @SerializedName("question_id")
    val questionId: String,
    @SerializedName("answer_id")
    val answerId: String
)

data class LogicalFinishRequest(
    @SerializedName("match_id")
    val matchId: String
)

data class LogicalAnswerResponse(
    val status: String?,
    @SerializedName("next_question")
    val nextQuestion: LogicalQuestion?,
    @SerializedName("current_question")
    val currentQuestion: Int?,
    @SerializedName("total_question")
    val totalQuestion: Int?,
    val complete: Boolean?
)

data class LogicalFinishResponse(
    val status: String?,
    @SerializedName("total_point")
    val totalPoint: Int?,
    val iq: Int?,
    val category: String?,
    val message: String?,
    @SerializedName("duration_seconds")
    val durationSeconds: Int?
)

data class LogicalStatisticsResponse(
    val status: String?,
    val name: String? = null,
    @SerializedName("total_match")
    val totalMatches: Int?,
    @SerializedName("highest_iq")
    val highestIq: Int?,
    @SerializedName("last_iq")
    val lastIq: Int?
)

// Intuition
data class IntuitionStartResponse(
    val status: String?,
    @SerializedName("match_id")
    val matchId: String?,
    @SerializedName("current_round")
    val currentRound: Int?,
    @SerializedName("total_rounds")
    val totalRounds: Int?,
    val message: String?
)

data class IntuitionRoundItem(
    val id: String,
    val name: String?,
    val image: String?,
    val description: String?
)

data class IntuitionRoundItemsResponse(
    @SerializedName("options")
    val options: List<IntuitionRoundItem>?,
    val round: Int? = null
)

data class IntuitionAnswerRequest(
    @SerializedName("chosen_item_id")
    val chosenItemId: String
)

data class IntuitionAnswerResponse(
    val correct: Boolean? = null,
    @SerializedName("correct_item_id")
    val correctItemId: String? = null,
    @SerializedName("match_completed")
    val matchCompleted: Boolean? = null,
    @SerializedName("next_round")
    val nextRound: Int? = null
)

data class IntuitionStatisticsResponse(
    val status: String?,
    @SerializedName("total_played")
    val totalPlayed: Int?,
    @SerializedName("total_correct")
    val totalCorrect: Int?,
    val level: Int?,
    @SerializedName("token_reward")
    val tokenReward: Int?
)

// Tarot
data class TarotStartResponse(
    val status: String?,
    @SerializedName("session_id")
    val sessionId: String?,
    val cards: List<TarotCardOption>?,
    val message: String? = null
)

data class TarotCardOption(
    val id: String,
    val orientation: String?
)

data class TarotCardSelection(
    val id: String,
    val orientation: String
)

data class TarotPickRequest(
    @SerializedName("session_id")
    val sessionId: String,
    val name: String?,
    @SerializedName("energy_choice")
    val energyChoice: String?,
    val cards: List<TarotCardSelection>
)

data class TarotPickCardResponse(
    val status: String? = null,
    val message: String? = null,
    val oracle: String? = null,
    @SerializedName("session_id")
    val sessionId: String? = null,
    val cards: List<TarotCardDetail>? = null
)

data class TarotReadingRequest(
    @SerializedName("session_id")
    val sessionId: String,
    @SerializedName("oracle_name")
    val oracleName: String?
)

data class TarotReadingResponse(
    val status: String?,
    @SerializedName("message")
    val reading: String?
)

data class TarotCardDetail(
    val name: String?,
    val orientation: String?,
    val image: String? = null
)

data class TarotHistoryItem(
    val id: Int,
    @SerializedName("user_id")
    val userId: Int? = null,
    @SerializedName("session_date")
    val sessionDate: String? = null,
    val readings: List<TarotReading>? = null
)

data class TarotReading(
    val id: Int? = null,
    @SerializedName("session_id")
    val sessionId: String? = null,
    val reading: String? = null,
    @SerializedName("created_at")
    val createdAt: String? = null
)

// Ulartangga
data class UlartanggaMatch(
    val id: Int,
    val status: String?,
    val players: List<String>?,
    @SerializedName("current_turn")
    val currentTurn: String?
)

data class UlartanggaCreateResponse(
    val status: String?,
    val match: UlartanggaMatch?
)

data class UlartanggaDiceResponse(
    val status: String?,
    val dice: Int?,
    val position: Int?,
    val event: String?
)

// Ngepet

data class NgepetAvatar(
    val id: Int,
    val name: String?,
    @SerializedName("image_url")
    val imageUrl: String?,
    val tier: String?,
    val type: String? = null,
    val price: Int? = null,
    val stock: Int? = null,
    val own: Int? = null
)

data class NgepetLobbyMatch(
    val id: String,
    @SerializedName("host_name")
    val hostName: String?,
    @SerializedName("house_avatar_id")
    val houseAvatarId: Int? = null,
    @SerializedName("token_pool")
    val tokenPool: Int?,
    @SerializedName("total_token_pool")
    val totalTokenPool: Int? = null,
    @SerializedName("min_intruder_token")
    val minIntruderToken: Int? = null,
    @SerializedName("max_intruder_token")
    val maxIntruderToken: Int? = null,
    val difficulty: String?,
    @SerializedName("guess_duration_hours")
    val guessDurationHours: Int?,
    @SerializedName("max_intruders")
    val maxIntruders: Int?,
    val status: String?,
    @SerializedName("intruders_count")
    val intrudersCount: Int?,
    @SerializedName("house_avatar")
    val houseAvatar: NgepetAvatar? = null
)

data class NgepetMatchesListResponse(
    val status: String?,
    val matches: List<NgepetLobbyMatch>?
)

data class NgepetActiveMatchData(
    @SerializedName("match_id")
    val matchId: String?,
    val role: String?,
    val status: String?,
    @SerializedName("token_pool")
    val tokenPool: Int? = null,
    @SerializedName("host_name")
    val hostName: String? = null,
    @SerializedName("intruders_count")
    val intrudersCount: Int? = null,
    @SerializedName("max_intruders")
    val maxIntruders: Int? = null,
    @SerializedName("intruder_match_id")
    val intruderMatchId: String? = null
)

data class NgepetActiveMatchResponse(
    val status: String?,
    val data: NgepetActiveMatchData?,
    val token: String? = null
)

data class NgepetMatchItem(
    val name: String?,
    @SerializedName("image_url")
    val imageUrl: String? = null
)

data class NgepetIntruder(
    val id: String,
    @SerializedName("intruder_name")
    val intruderName: String?,
    @SerializedName("avatar_id")
    val avatarId: Int? = null,
    val status: String?,
    @SerializedName("intruders_at")
    val intrudersAt: String? = null,
    @SerializedName("guess_deadline")
    val guessDeadline: String? = null,
    @SerializedName("is_pick_choice")
    val isPickChoice: Int? = null,
    val result: String? = null,
    @SerializedName("token_pool")
    val tokenPool: Int? = null,
    val avatar: NgepetAvatar? = null
)

data class NgepetEvent(
    val role: String?,
    val details: String?,
    @SerializedName("created_at")
    val createdAt: String?
)

data class NgepetHiddenItem(
    val id: Int?,
    val status: String?,
    val result: String? = null
)

data class NgepetMatchDetail(
    val id: String,
    @SerializedName("host_name")
    val hostName: String?,
    @SerializedName("token_pool")
    val tokenPool: Int?,
    @SerializedName("total_token_pool")
    val totalTokenPool: Int? = null,
    val difficulty: String?,
    @SerializedName("guess_duration_hours")
    val guessDurationHours: Int?,
    @SerializedName("max_intruders")
    val maxIntruders: Int?,
    val status: String?,
    @SerializedName("house_avatar")
    val houseAvatar: NgepetAvatar? = null,
    val items: List<NgepetMatchItem>? = null,
    val intruders: List<NgepetIntruder>? = null,
    @SerializedName("intruders_count")
    val intrudersCount: Int? = null,
    @SerializedName("hidden_tokens_count")
    val hiddenTokensCount: Int? = null,
    val events: List<NgepetEvent>? = null,
    @SerializedName("hidden_items")
    val hiddenItems: List<NgepetHiddenItem>? = null
)

data class NgepetMatchDetailResponse(
    val match: NgepetMatchDetail?
)

data class NgepetCreateMatchRequest(
    @SerializedName("host_name")
    val hostName: String,
    val difficulty: String,
    @SerializedName("guess_duration_hours")
    val guessDurationHours: Int,
    @SerializedName("max_intruders")
    val maxIntruders: Int,
    @SerializedName("token_pool")
    val tokenPool: Int,
    @SerializedName("min_intruder_token")
    val minIntruderToken: Int? = null,
    @SerializedName("max_intruder_token")
    val maxIntruderToken: Int? = null,
    @SerializedName("house_avatar_id")
    val houseAvatarId: Int? = null
)

data class NgepetCreateMatchResponse(
    val status: String?,
    val message: String?,
    val id: String?
)

data class NgepetJoinRequest(
    val name: String,
    @SerializedName("token_amount")
    val tokenAmount: Int,
    @SerializedName("avatar_id")
    val avatarId: Int? = null
)

data class NgepetSubmitChoiceRequest(
    @SerializedName("item_name")
    val itemName: String
)

data class NgepetHiddenItemRequest(
    @SerializedName("item_name")
    val itemName: String
)

data class NgepetHostGuessRequest(
    @SerializedName("match_intruder_id")
    val matchIntruderId: String,
    @SerializedName("item_name")
    val itemName: String
)

data class NgepetHiddenGuessRequest(
    @SerializedName("match_intruder_id")
    val matchIntruderId: String,
    @SerializedName("item_name")
    val itemName: String
)

data class NgepetGuessResponse(
    val status: String?,
    @SerializedName("is_correct")
    val isCorrect: Boolean?,
    @SerializedName("is_end")
    val isEnd: Boolean?,
    @SerializedName("answer_item")
    val answerItem: String? = null
)

data class NgepetClaimVictoryRequest(
    @SerializedName("match_intruder_id")
    val matchIntruderId: String
)

data class NgepetAvatarShopResponse(
    val status: String?,
    val data: List<NgepetAvatar>?
)

data class NgepetOwnedAvatarItem(
    val id: Int,
    @SerializedName("avatar_id")
    val avatarId: Int,
    @SerializedName("is_equipped")
    val isEquipped: Boolean?,
    val avatar: NgepetAvatar?
)

data class NgepetOwnedAvatarsResponse(
    val status: String?,
    val data: List<NgepetOwnedAvatarItem>?
)

data class NgepetHistoryItem(
    @SerializedName("match_id")
    val matchId: String?,
    @SerializedName("host_name")
    val hostName: String?,
    val status: String?,
    val role: String?,
    @SerializedName("match_result")
    val matchResult: String?,
    @SerializedName("created_at")
    val createdAt: String?
)

data class NgepetHistoryResponse(
    val status: String?,
    val data: List<NgepetHistoryItem>?
)

data class NgepetLeaderboardEntry(
    val name: String?,
    @SerializedName("host_name")
    val hostName: String? = null,
    @SerializedName("intruder_name")
    val intruderName: String? = null,
    val wins: Int? = null,
    @SerializedName("total_wins")
    val totalWins: Int? = null,
    @SerializedName("total_matches")
    val totalMatches: Int? = null,
    @SerializedName("total_intruder_games")
    val totalIntruderGames: Int? = null,
    @SerializedName("total_games")
    val totalGames: Int? = null,
    @SerializedName("token_pool")
    val tokenPool: Int? = null,
    val winrate: Double? = null,
    @SerializedName("winrate_percentage")
    val winratePercentage: Double? = null,
    @SerializedName("win_rate")
    val winRate: Double? = null
)

data class NgepetLeaderboardResponse(
    val status: String?,
    val data: List<NgepetLeaderboardEntry>?
)
