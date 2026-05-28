package com.mysticnusa.app.data.repository

import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.remote.RetrofitInstance

class GamesRepository {

    private val api = RetrofitInstance.api

    // Trivia
    suspend fun startTrivia(request: TriviaStartRequest): Result<TriviaStartResponse> {
        return try {
            val response = api.startTrivia(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to start trivia: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun answerTrivia(request: TriviaAnswerRequest): Result<TriviaAnswerResponse> {
        return try {
            val response = api.answerTrivia(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to answer trivia: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun finishTrivia(request: TriviaFinishRequest): Result<TriviaFinishResponse> {
        return try {
            val response = api.finishTrivia(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to finish trivia: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getTriviaLeaderboard(): Result<LeaderboardResponse> {
        return try {
            val response = api.getTriviaLeaderboard()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get leaderboard: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    // Logical
    suspend fun startLogical(): Result<LogicalStartResponse> {
        return try {
            val response = api.startLogical()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to start logical: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun answerLogical(request: LogicalAnswerRequest): Result<LogicalAnswerResponse> {
        return try {
            val response = api.answerLogical(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to answer logical: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun finishLogical(sessionId: String): Result<LogicalFinishResponse> {
        return try {
            val response = api.finishLogical(TriviaFinishRequest(sessionId))
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to finish logical: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getLogicalLeaderboard(): Result<LeaderboardResponse> {
        return try {
            val response = api.getLogicalLeaderboard()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get logical leaderboard: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getLogicalStatistics(): Result<LogicalStatisticsResponse> {
        return try {
            val response = api.getLogicalStatistics()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get logical statistics: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    // Intuition
    suspend fun startIntuition(): Result<IntuitionStartResponse> {
        return try {
            val response = api.startIntuition()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to start intuition: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getIntuitionRoundItems(matchId: String): Result<IntuitionRoundItemsResponse> {
        return try {
            val response = api.getIntuitionRoundItems(matchId)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get round items: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun answerIntuition(matchId: String, request: IntuitionAnswerRequest): Result<IntuitionAnswerResponse> {
        return try {
            val response = api.answerIntuition(matchId, request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to answer intuition: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getIntuitionLeaderboard(): Result<LeaderboardResponse> {
        return try {
            val response = api.getIntuitionLeaderboard()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get intuition leaderboard: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getIntuitionStatistics(): Result<IntuitionStatisticsResponse> {
        return try {
            val response = api.getIntuitionStatistics()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get intuition statistics: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    // Tarot
    suspend fun startTarot(): Result<TarotStartResponse> {
        return try {
            val response = api.startTarot()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to start tarot: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun pickTarotCards(request: TarotPickRequest): Result<ApiResponse> {
        return try {
            val response = api.pickTarotCards(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to pick tarot cards: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getTarotReading(request: TarotReadingRequest): Result<TarotReadingResponse> {
        return try {
            val response = api.getTarotReading(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get tarot reading: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getTarotHistory(): Result<List<TarotHistoryItem>> {
        return try {
            val response = api.getTarotHistory()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get tarot history: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    // Ulartangga
    suspend fun getUlartanggaMatches(): Result<List<UlartanggaMatch>> {
        return try {
            val response = api.getUlartanggaMatches()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get ulartangga matches: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun createUlartanggaMatch(): Result<UlartanggaCreateResponse> {
        return try {
            val response = api.createUlartanggaMatch()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to create ulartangga match: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun joinUlartanggaMatch(matchId: Int): Result<ApiResponse> {
        return try {
            val response = api.joinUlartanggaMatch(mapOf("match_id" to matchId))
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to join ulartangga match: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun throwDice(matchId: Int): Result<UlartanggaDiceResponse> {
        return try {
            val response = api.throwDice(mapOf("match_id" to matchId))
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to throw dice: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    // Ngepet
    suspend fun getNgepetMatches(): Result<NgepetMatchesResponse> {
        return try {
            val response = api.getNgepetMatches()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to get ngepet matches: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun createNgepetMatch(): Result<ApiResponse> {
        return try {
            val response = api.createNgepetMatch()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to create ngepet match: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun joinNgepetMatch(id: Int): Result<ApiResponse> {
        return try {
            val response = api.joinNgepetMatch(id)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to join ngepet match: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun submitNgepetChoice(id: Int, choice: String): Result<ApiResponse> {
        return try {
            val response = api.submitNgepetChoice(id, NgepetChoiceRequest(choice))
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to submit ngepet choice: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun guessNgepet(id: Int, guess: String): Result<ApiResponse> {
        return try {
            val response = api.guessNgepet(id, NgepetGuessRequest(guess))
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                Result.failure(Exception("Failed to guess ngepet: ${response.code()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
