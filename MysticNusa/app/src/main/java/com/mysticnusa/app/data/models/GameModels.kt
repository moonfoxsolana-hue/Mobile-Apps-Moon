package com.mysticnusa.app.data.models

import com.google.gson.annotations.SerializedName

// Common
data class ApiResponse(
    val status: String?,
    val message: String?
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
    @SerializedName("question_text")
    val question: String?,
    val answers: List<LogicalAnswer>?
)

data class LogicalAnswer(
    val id: String,
    @SerializedName("answer_text")
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
    @SerializedName("image_url")
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
data class NgepetMatch(
    val id: Int,
    val status: String?,
    val players: List<String>?,
    @SerializedName("max_players")
    val maxPlayers: Int?
)

data class NgepetMatchesResponse(
    val status: String?,
    val matches: List<NgepetMatch>?
)

data class NgepetChoiceRequest(
    val choice: String
)

data class NgepetGuessRequest(
    val guess: String
)
