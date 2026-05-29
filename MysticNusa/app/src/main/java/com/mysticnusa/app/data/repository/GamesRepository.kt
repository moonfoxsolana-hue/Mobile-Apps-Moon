package com.mysticnusa.app.data.repository

import com.google.gson.Gson
import com.google.gson.JsonObject
import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.remote.RetrofitInstance

class GamesRepository {

    private val api = RetrofitInstance.api

    private fun parseErrorMessage(errorBody: String?, fallback: String): String {
        if (errorBody.isNullOrBlank()) return fallback
        return try {
            val json = Gson().fromJson(errorBody, JsonObject::class.java)
            json.get("message")?.asString ?: fallback
        } catch (e: Exception) {
            fallback
        }
    }

    // Trivia
    suspend fun startTrivia(request: TriviaStartRequest): Result<TriviaStartResponse> {
        return try {
            val response = api.startTrivia(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to start trivia: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to answer trivia: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to finish trivia: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getTriviaLeaderboard(): Result<List<LeaderboardEntry>> {
        return try {
            val response = api.getTriviaLeaderboard()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get leaderboard: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to start logical: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to answer logical: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun finishLogical(matchId: String): Result<LogicalFinishResponse> {
        return try {
            val response = api.finishLogical(LogicalFinishRequest(matchId))
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to finish logical: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getLogicalLeaderboard(): Result<List<LeaderboardEntry>> {
        return try {
            val response = api.getLogicalLeaderboard()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get logical leaderboard: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get logical statistics: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to start intuition: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get round items: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to answer intuition: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getIntuitionLeaderboard(): Result<List<LeaderboardEntry>> {
        return try {
            val response = api.getIntuitionLeaderboard()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get intuition leaderboard: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get intuition statistics: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to start tarot: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun pickTarotCards(request: TarotPickRequest): Result<TarotPickCardResponse> {
        return try {
            val response = api.pickTarotCards(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to pick tarot cards: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get tarot reading: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get tarot history: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to get ulartangga matches: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to create ulartangga match: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to join ulartangga match: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
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
                val errorMsg = parseErrorMessage(
                    response.errorBody()?.string(),
                    "Failed to throw dice: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    // Ngepet error parser - checks 'error' key first, then 'message'
    private fun parseNgepetError(errorBody: String?, fallback: String): String {
        if (errorBody.isNullOrBlank()) return fallback
        return try {
            val json = Gson().fromJson(errorBody, JsonObject::class.java)
            json.get("error")?.asString
                ?: json.get("message")?.asString
                ?: fallback
        } catch (e: Exception) {
            fallback
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
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get ngepet matches: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getNgepetActiveMatch(): Result<NgepetActiveMatchResponse> {
        return try {
            val response = api.getNgepetActiveMatch()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get active match: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getNgepetMatchDetail(id: String): Result<NgepetMatchDetailResponse> {
        return try {
            val response = api.getNgepetMatchDetail(id)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get match detail: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun createNgepetMatch(request: NgepetCreateRequest): Result<NgepetCreateResponse> {
        return try {
            val response = api.createNgepetMatch(request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to create ngepet match: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun joinNgepetMatch(id: String, request: NgepetJoinRequest): Result<String> {
        return try {
            val response = api.joinNgepetMatch(id, request)
            if (response.isSuccessful) {
                val body = response.body()
                val message = body?.get("success")?.asString ?: "Joined successfully"
                Result.success(message)
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to join ngepet match: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun submitNgepetChoice(id: String, request: NgepetSubmitChoiceRequest): Result<String> {
        return try {
            val response = api.submitNgepetChoice(id, request)
            if (response.isSuccessful) {
                val body = response.body()
                val message = body?.get("success")?.asString ?: "Choice submitted"
                Result.success(message)
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to submit choice: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun ngepetHostGuess(id: String, request: NgepetHostGuessRequest): Result<NgepetGuessResponse> {
        return try {
            val response = api.ngepetHostGuess(id, request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to guess: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun ngepetStoreHiddenItem(id: String, request: NgepetHiddenItemRequest): Result<String> {
        return try {
            val response = api.ngepetStoreHiddenItem(id, request)
            if (response.isSuccessful) {
                val message = response.body()?.message ?: "Hidden item stored"
                Result.success(message)
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to store hidden item: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun ngepetMakeHiddenGuess(id: String, request: NgepetHiddenGuessRequest): Result<NgepetGuessResponse> {
        return try {
            val response = api.ngepetMakeHiddenGuess(id, request)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to guess hidden item: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun closeNgepetMatch(id: String): Result<NgepetCloseResponse> {
        return try {
            val response = api.closeNgepetMatch(id)
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to close ngepet match: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun claimNgepetVictory(request: NgepetClaimVictoryRequest): Result<String> {
        return try {
            val response = api.claimNgepetVictory(request)
            if (response.isSuccessful) {
                val message = response.body()?.message ?: "Victory claimed"
                Result.success(message)
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to claim victory: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getNgepetAvatarShop(): Result<NgepetAvatarShopResponse> {
        return try {
            val response = api.getNgepetAvatarShop()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get avatar shop: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun buyNgepetAvatar(id: Int): Result<String> {
        return try {
            val response = api.buyNgepetAvatar(id)
            if (response.isSuccessful) {
                val message = response.body()?.message ?: "Avatar purchased"
                Result.success(message)
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to buy avatar: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getNgepetOwnedAvatars(): Result<NgepetOwnedAvatarsResponse> {
        return try {
            val response = api.getNgepetOwnedAvatars()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get owned avatars: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun equipNgepetAvatar(id: Int): Result<String> {
        return try {
            val response = api.equipNgepetAvatar(id)
            if (response.isSuccessful) {
                val message = response.body()?.message ?: "Avatar equipped"
                Result.success(message)
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to equip avatar: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getNgepetHistory(): Result<NgepetHistoryResponse> {
        return try {
            val response = api.getNgepetHistory()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get match history: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getNgepetLeaderboardHouse(): Result<JsonObject> {
        return try {
            val response = api.getNgepetLeaderboardHouse()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get house leaderboard: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getNgepetLeaderboardHost(): Result<JsonObject> {
        return try {
            val response = api.getNgepetLeaderboardHost()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get host leaderboard: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getNgepetLeaderboardIntruders(): Result<JsonObject> {
        return try {
            val response = api.getNgepetLeaderboardIntruders()
            if (response.isSuccessful) {
                response.body()?.let { Result.success(it) }
                    ?: Result.failure(Exception("Empty response body"))
            } else {
                val errorMsg = parseNgepetError(
                    response.errorBody()?.string(),
                    "Failed to get intruder leaderboard: ${response.code()}"
                )
                Result.failure(Exception(errorMsg))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
