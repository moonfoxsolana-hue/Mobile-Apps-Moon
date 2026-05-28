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
    val highestScore: Int?,
    @SerializedName("highest_iq")
    val highestIq: Int?,
    val score: Int?,
    val rank: Int?
)

data class LeaderboardResponse(
    val status: String?,
    val leaderboard: List<LeaderboardEntry>?
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
    val sessionId: String?,
    val question: TriviaQuestion?,
    @SerializedName("current_question")
    val currentQuestion: Int?,
    @SerializedName("total_question")
    val totalQuestion: Int?,
    val complete: Boolean?
)

data class TriviaAnswerRequest(
    @SerializedName("session_id")
    val sessionId: String,
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
    val sessionId: String
)

data class TriviaFinishResponse(
    val status: String?,
    val score: Int?,
    val streak: Int?,
    @SerializedName("duration_seconds")
    val durationSeconds: Int?
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
    val totalQuestion: Int?
)

data class LogicalQuestion(
    val id: Int,
    val question: String?,
    val answers: List<LogicalAnswer>?
)

data class LogicalAnswer(
    val id: Int,
    val text: String?,
    val value: Int?
)

data class LogicalAnswerRequest(
    @SerializedName("match_id")
    val matchId: String,
    @SerializedName("question_id")
    val questionId: Int,
    @SerializedName("answer_id")
    val answerId: Int
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
    @SerializedName("total_matches")
    val totalMatches: Int?,
    @SerializedName("highest_iq")
    val highestIq: Int?,
    @SerializedName("average_iq")
    val averageIq: Double?
)

// Intuition
data class IntuitionStartResponse(
    val status: String?,
    @SerializedName("match_id")
    val matchId: String?,
    @SerializedName("current_round")
    val currentRound: Int?,
    @SerializedName("total_rounds")
    val totalRounds: Int?
)

data class IntuitionRoundItem(
    val id: Int,
    val name: String?,
    val image: String?
)

data class IntuitionRoundItemsResponse(
    val status: String?,
    val items: List<IntuitionRoundItem>?
)

data class IntuitionAnswerRequest(
    @SerializedName("chosen_item_id")
    val chosenItemId: Int
)

data class IntuitionAnswerResponse(
    val status: String?,
    @SerializedName("is_correct")
    val isCorrect: Boolean?,
    @SerializedName("correct_item")
    val correctItem: IntuitionRoundItem?,
    @SerializedName("current_round")
    val currentRound: Int?,
    @SerializedName("total_rounds")
    val totalRounds: Int?,
    val complete: Boolean?,
    val score: Int?
)

data class IntuitionStatisticsResponse(
    val status: String?,
    @SerializedName("total_matches")
    val totalMatches: Int?,
    @SerializedName("highest_score")
    val highestScore: Int?
)

// Tarot
data class TarotStartResponse(
    val status: String?,
    @SerializedName("session_id")
    val sessionId: String?,
    val cards: List<TarotCardOption>?
)

data class TarotCardOption(
    val id: Int,
    val orientation: String?
)

data class TarotPickRequest(
    @SerializedName("session_id")
    val sessionId: String,
    val name: String?,
    @SerializedName("energy_choice")
    val energyChoice: String?,
    val cards: List<Int>
)

data class TarotReadingRequest(
    @SerializedName("session_id")
    val sessionId: String,
    @SerializedName("oracle_name")
    val oracleName: String?
)

data class TarotReadingResponse(
    val status: String?,
    val reading: String?,
    val cards: List<TarotCardDetail>?
)

data class TarotCardDetail(
    val id: Int,
    val name: String?,
    val orientation: String?,
    val meaning: String?
)

data class TarotHistoryItem(
    val id: Int,
    @SerializedName("session_id")
    val sessionId: String?,
    val reading: String?,
    @SerializedName("created_at")
    val createdAt: String?
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
