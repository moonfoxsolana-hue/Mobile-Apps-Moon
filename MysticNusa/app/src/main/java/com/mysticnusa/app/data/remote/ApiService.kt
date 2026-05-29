package com.mysticnusa.app.data.remote

import com.google.gson.JsonObject
import com.mysticnusa.app.data.models.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    // Auth
    @POST("register")
    suspend fun register(@Body request: RegisterRequest): Response<AuthResponse>

    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<AuthResponse>

    @POST("logout")
    suspend fun logout(): Response<ApiResponse>

    // Profile
    @GET("profile")
    suspend fun getProfile(): Response<UserProfile>

    @GET("token-history")
    suspend fun getTokenHistory(): Response<List<TokenHistoryItem>>

    // Airdrop
    @POST("airdrop/claim")
    suspend fun claimAirdrop(@Body request: AirdropClaimRequest): Response<AirdropClaimResponse>

    @POST("airdrop/claim-with-code")
    suspend fun claimWithCode(@Body request: AirdropCodeRequest): Response<AirdropClaimResponse>

    // News
    @GET("news")
    suspend fun getNews(@Query("page") page: Int = 1): Response<PaginatedResponse<NewsItem>>

    // Stories
    @GET("stories")
    suspend fun getStories(@Query("page") page: Int = 1): Response<PaginatedResponse<StoryItem>>

    // Staking
    @GET("staking/types")
    suspend fun getStakingTypes(): Response<List<StakingType>>

    @POST("staking")
    suspend fun stake(@Body request: StakeRequest): Response<ApiResponse>

    @GET("user/stakings")
    suspend fun getUserStakings(): Response<List<UserStaking>>

    @POST("staking/claim/{id}")
    suspend fun claimStakingReward(@Path("id") id: Int): Response<ApiResponse>

    @DELETE("staking/cancel/{id}")
    suspend fun cancelStaking(@Path("id") id: Int): Response<ApiResponse>

    // Trivia
    @POST("trivia/start")
    suspend fun startTrivia(@Body request: TriviaStartRequest): Response<TriviaStartResponse>

    @POST("trivia/answer")
    suspend fun answerTrivia(@Body request: TriviaAnswerRequest): Response<TriviaAnswerResponse>

    @POST("trivia/finish")
    suspend fun finishTrivia(@Body request: TriviaFinishRequest): Response<TriviaFinishResponse>

    @GET("trivia/leaderboard")
    suspend fun getTriviaLeaderboard(): Response<List<LeaderboardEntry>>

    // Logical
    @POST("logical/start")
    suspend fun startLogical(): Response<LogicalStartResponse>

    @POST("logical/answer")
    suspend fun answerLogical(@Body request: LogicalAnswerRequest): Response<LogicalAnswerResponse>

    @POST("logical/finish")
    suspend fun finishLogical(@Body request: LogicalFinishRequest): Response<LogicalFinishResponse>

    @GET("logical/leaderboard")
    suspend fun getLogicalLeaderboard(): Response<List<LeaderboardEntry>>

    @GET("logical/statistics")
    suspend fun getLogicalStatistics(): Response<LogicalStatisticsResponse>

    // Intuition
    @POST("intuition/start")
    suspend fun startIntuition(): Response<IntuitionStartResponse>

    @GET("intuition/round/{matchId}")
    suspend fun getIntuitionRoundItems(@Path("matchId") matchId: String): Response<IntuitionRoundItemsResponse>

    @POST("intuition/answer/{matchId}")
    suspend fun answerIntuition(
        @Path("matchId") matchId: String,
        @Body request: IntuitionAnswerRequest
    ): Response<IntuitionAnswerResponse>

    @GET("intuition/leaderboard")
    suspend fun getIntuitionLeaderboard(): Response<List<LeaderboardEntry>>

    @GET("intuition/statistics")
    suspend fun getIntuitionStatistics(): Response<IntuitionStatisticsResponse>

    // Tarot
    @POST("tarot/start")
    suspend fun startTarot(): Response<TarotStartResponse>

    @POST("tarot/pick-card")
    suspend fun pickTarotCards(@Body request: TarotPickRequest): Response<TarotPickCardResponse>

    @POST("tarot/ai-reading")
    suspend fun getTarotReading(@Body request: TarotReadingRequest): Response<TarotReadingResponse>

    @GET("tarot/history")
    suspend fun getTarotHistory(): Response<List<TarotHistoryItem>>

    // Ulartangga
    @GET("ulartangga/list-match")
    suspend fun getUlartanggaMatches(): Response<List<UlartanggaMatch>>

    @GET("ulartangga/active-match")
    suspend fun getUlartanggaActiveMatch(): Response<UlartanggaMatch>

    @POST("ulartangga/create-match")
    suspend fun createUlartanggaMatch(): Response<UlartanggaCreateResponse>

    @POST("ulartangga/join-match")
    suspend fun joinUlartanggaMatch(@Body body: Map<String, Int>): Response<ApiResponse>

    @POST("ulartangga/match/start")
    suspend fun startUlartanggaMatch(@Body body: Map<String, Int>): Response<ApiResponse>

    @POST("ulartangga/match/throw-dice")
    suspend fun throwDice(@Body body: Map<String, Int>): Response<UlartanggaDiceResponse>

    @POST("ulartangga/match/ongoing-match")
    suspend fun getUlartanggaOngoingMatch(@Body body: Map<String, Int>): Response<ApiResponse>

    @GET("ulartangga/leaderboard")
    suspend fun getUlartanggaLeaderboard(): Response<List<LeaderboardEntry>>

    @GET("ulartangga/statistics")
    suspend fun getUlartanggaStatistics(): Response<ApiResponse>

    // Ngepet - Matches
    @GET("ngepet/match")
    suspend fun getNgepetMatches(): Response<NgepetMatchesResponse>

    @GET("ngepet/match/active")
    suspend fun getNgepetActiveMatch(): Response<NgepetActiveMatchResponse>

    @GET("ngepet/match/history")
    suspend fun getNgepetHistory(): Response<NgepetHistoryResponse>

    @GET("ngepet/match/{id}")
    suspend fun getNgepetMatchDetail(@Path("id") id: String): Response<NgepetMatchDetailResponse>

    @POST("ngepet/match/create")
    suspend fun createNgepetMatch(@Body request: NgepetCreateRequest): Response<NgepetCreateResponse>

    @POST("ngepet/match/{id}/join")
    suspend fun joinNgepetMatch(@Path("id") id: String, @Body request: NgepetJoinRequest): Response<JsonObject>

    @POST("ngepet/match/{id}/submit-choice")
    suspend fun submitNgepetChoice(@Path("id") id: String, @Body request: NgepetSubmitChoiceRequest): Response<JsonObject>

    @POST("ngepet/match/{id}/guess")
    suspend fun ngepetHostGuess(@Path("id") id: String, @Body request: NgepetHostGuessRequest): Response<NgepetGuessResponse>

    @POST("ngepet/match/{id}/hidden-item")
    suspend fun ngepetStoreHiddenItem(@Path("id") id: String, @Body request: NgepetHiddenItemRequest): Response<ApiResponse>

    @POST("ngepet/match/{id}/hidden-guess")
    suspend fun ngepetMakeHiddenGuess(@Path("id") id: String, @Body request: NgepetHiddenGuessRequest): Response<NgepetGuessResponse>

    @POST("ngepet/match/{id}/close")
    suspend fun closeNgepetMatch(@Path("id") id: String): Response<NgepetCloseResponse>

    @POST("ngepet/match/claim-victory")
    suspend fun claimNgepetVictory(@Body request: NgepetClaimVictoryRequest): Response<ApiResponse>

    // Ngepet - Avatars
    @GET("ngepet/avatar")
    suspend fun getNgepetAvatarShop(): Response<NgepetAvatarShopResponse>

    @POST("ngepet/avatar/{id}/buy")
    suspend fun buyNgepetAvatar(@Path("id") id: Int): Response<ApiResponse>

    @GET("ngepet/avatar/own")
    suspend fun getNgepetOwnedAvatars(): Response<NgepetOwnedAvatarsResponse>

    @POST("ngepet/avatar/{id}/equip")
    suspend fun equipNgepetAvatar(@Path("id") id: Int): Response<ApiResponse>

    // Ngepet - Leaderboards
    @GET("ngepet/leaderboard/house")
    suspend fun getNgepetLeaderboardHouse(): Response<JsonObject>

    @GET("ngepet/leaderboard/host")
    suspend fun getNgepetLeaderboardHost(): Response<JsonObject>

    @GET("ngepet/leaderboard/intruders")
    suspend fun getNgepetLeaderboardIntruders(): Response<JsonObject>
}
