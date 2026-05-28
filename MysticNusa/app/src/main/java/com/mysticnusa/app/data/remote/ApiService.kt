package com.mysticnusa.app.data.remote

import com.mysticnusa.app.data.models.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    // Auth
    @POST("auth/register")
    suspend fun register(@Body request: RegisterRequest): Response<AuthResponse>

    @POST("auth/login")
    suspend fun login(@Body request: LoginRequest): Response<AuthResponse>

    @POST("auth/logout")
    suspend fun logout(): Response<ApiResponse>

    @POST("auth/wallet")
    suspend fun updateWallet(@Body request: WalletRequest): Response<ApiResponse>

    // Profile
    @GET("profile")
    suspend fun getProfile(): Response<ProfileResponse>

    @GET("profile/token-history")
    suspend fun getTokenHistory(): Response<TokenHistoryResponse>

    // Airdrop
    @POST("airdrop/claim")
    suspend fun claimAirdrop(@Body request: AirdropClaimRequest): Response<AirdropClaimResponse>

    @POST("airdrop/claim-code")
    suspend fun claimWithCode(@Body request: AirdropCodeRequest): Response<AirdropClaimResponse>

    // News
    @GET("news")
    suspend fun getNews(@Query("page") page: Int = 1): Response<PaginatedResponse<NewsItem>>

    // Stories
    @GET("stories")
    suspend fun getStories(@Query("page") page: Int = 1): Response<PaginatedResponse<StoryItem>>

    // Staking
    @GET("staking/types")
    suspend fun getStakingTypes(): Response<StakingTypesResponse>

    @POST("staking/stake")
    suspend fun stake(@Body request: StakeRequest): Response<ApiResponse>

    @GET("staking")
    suspend fun getUserStakings(): Response<UserStakingsResponse>

    @POST("staking/{id}/claim")
    suspend fun claimStakingReward(@Path("id") id: Int): Response<ApiResponse>

    @POST("staking/{id}/cancel")
    suspend fun cancelStaking(@Path("id") id: Int): Response<ApiResponse>

    // Games - Trivia
    @POST("games/trivia/start")
    suspend fun startTrivia(@Body request: TriviaStartRequest): Response<TriviaStartResponse>

    @POST("games/trivia/answer")
    suspend fun answerTrivia(@Body request: TriviaAnswerRequest): Response<TriviaAnswerResponse>

    @POST("games/trivia/finish")
    suspend fun finishTrivia(@Body request: TriviaFinishRequest): Response<TriviaFinishResponse>

    @GET("games/trivia/leaderboard")
    suspend fun getTriviaLeaderboard(): Response<LeaderboardResponse>

    // Games - Logical
    @POST("games/logical/start")
    suspend fun startLogical(): Response<LogicalStartResponse>

    @POST("games/logical/answer")
    suspend fun answerLogical(@Body request: LogicalAnswerRequest): Response<LogicalAnswerResponse>

    @POST("games/logical/finish")
    suspend fun finishLogical(@Body request: TriviaFinishRequest): Response<LogicalFinishResponse>

    @GET("games/logical/leaderboard")
    suspend fun getLogicalLeaderboard(): Response<LeaderboardResponse>

    @GET("games/logical/statistics")
    suspend fun getLogicalStatistics(): Response<LogicalStatisticsResponse>

    // Games - Intuition
    @POST("games/intuition/start")
    suspend fun startIntuition(): Response<IntuitionStartResponse>

    @GET("games/intuition/{matchId}/round-items")
    suspend fun getIntuitionRoundItems(@Path("matchId") matchId: String): Response<IntuitionRoundItemsResponse>

    @POST("games/intuition/{matchId}/answer")
    suspend fun answerIntuition(
        @Path("matchId") matchId: String,
        @Body request: IntuitionAnswerRequest
    ): Response<IntuitionAnswerResponse>

    @GET("games/intuition/leaderboard")
    suspend fun getIntuitionLeaderboard(): Response<LeaderboardResponse>

    @GET("games/intuition/statistics")
    suspend fun getIntuitionStatistics(): Response<IntuitionStatisticsResponse>

    // Games - Tarot
    @POST("games/tarot/start")
    suspend fun startTarot(): Response<TarotStartResponse>

    @POST("games/tarot/pick-cards")
    suspend fun pickTarotCards(@Body request: TarotPickRequest): Response<ApiResponse>

    @POST("games/tarot/ai-reading")
    suspend fun getTarotReading(@Body request: TarotReadingRequest): Response<TarotReadingResponse>

    @GET("games/tarot/history")
    suspend fun getTarotHistory(): Response<List<TarotHistoryItem>>

    // Games - Ulartangga
    @GET("games/ulartangga/matches")
    suspend fun getUlartanggaMatches(): Response<List<UlartanggaMatch>>

    @POST("games/ulartangga/create")
    suspend fun createUlartanggaMatch(): Response<UlartanggaCreateResponse>

    @POST("games/ulartangga/join")
    suspend fun joinUlartanggaMatch(@Body body: Map<String, Int>): Response<ApiResponse>

    @POST("games/ulartangga/start")
    suspend fun startUlartanggaMatch(@Body body: Map<String, Int>): Response<ApiResponse>

    @POST("games/ulartangga/throw-dice")
    suspend fun throwDice(@Body body: Map<String, Int>): Response<UlartanggaDiceResponse>

    // Games - Ngepet
    @GET("games/ngepet/matches")
    suspend fun getNgepetMatches(): Response<NgepetMatchesResponse>

    @POST("games/ngepet/create")
    suspend fun createNgepetMatch(): Response<ApiResponse>

    @POST("games/ngepet/{id}/join")
    suspend fun joinNgepetMatch(@Path("id") id: Int): Response<ApiResponse>

    @POST("games/ngepet/{id}/submit-choice")
    suspend fun submitNgepetChoice(
        @Path("id") id: Int,
        @Body request: NgepetChoiceRequest
    ): Response<ApiResponse>

    @POST("games/ngepet/{id}/guess")
    suspend fun guessNgepet(
        @Path("id") id: Int,
        @Body request: NgepetGuessRequest
    ): Response<ApiResponse>
}
