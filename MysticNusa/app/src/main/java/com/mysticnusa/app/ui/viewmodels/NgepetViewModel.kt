package com.mysticnusa.app.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.remote.RetrofitInstance
import com.mysticnusa.app.data.repository.GamesRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

enum class NgepetPhase {
    LOBBY, CREATE_MATCH, JOIN_MATCH, MATCH_ROOM, AVATAR_SHOP, LEADERBOARD, HISTORY, RULES
}

data class NgepetUiState(
    val isLoading: Boolean = false,
    val phase: NgepetPhase = NgepetPhase.LOBBY,
    val tokenBalance: String? = null,
    val matches: List<NgepetMatchListItem> = emptyList(),
    val activeMatchData: NgepetActiveMatchData? = null,
    val matchDetail: NgepetMatchDetailResponse? = null,
    val ownedAvatars: List<NgepetOwnedAvatar> = emptyList(),
    val avatarShop: List<NgepetAvatarShopItem> = emptyList(),
    val history: List<NgepetHistoryItem> = emptyList(),
    val leaderboard: List<Any> = emptyList(),
    val leaderboardHouse: List<NgepetLeaderboardHouseItem> = emptyList(),
    val leaderboardHost: List<NgepetLeaderboardHostItem> = emptyList(),
    val leaderboardIntruder: List<NgepetLeaderboardIntruderItem> = emptyList(),
    val leaderboardType: String = "house",
    val currentRole: String? = null,
    val currentMatchId: String? = null,
    val currentIntruderMatchId: String? = null,
    val guessResult: NgepetGuessResponse? = null,
    val message: String? = null,
    val error: String? = null,
    val notificationCounter: Int = 0,
    val selectedMatchForJoin: NgepetMatchListItem? = null,
    val showMatchDetailDialog: Boolean = false,
    // Guessing animation state
    val isGuessing: Boolean = false,
    // Intruder guess attempt tracking
    val intruderGuessCount: Int = 0,
    val maxGuessAttempts: Int = 5,
    // Host guess re-open flow
    val lastGuessedIntruderId: String? = null,
    val shouldReopenGuess: Boolean = false,
    // Intruder hidden-item selection
    val selectedHiddenItemId: String? = null,
    val guessedItemNames: Set<String> = emptySet(),
    val showHiddenItemSelection: Boolean = false,
    val showGuessItemDialog: Boolean = false,
    // Create form fields
    val createHostName: String = "",
    val createDifficulty: String = "easy",
    val createDuration: Int = 3,
    val createMaxIntruders: Int = 1,
    val createTokenPool: String = "",
    val createMinToken: String = "",
    val createMaxToken: String = "",
    val createHouseAvatarId: Int? = null,
    // Join form fields
    val joinName: String = "",
    val joinTokenAmount: String = "",
    val joinPlayerAvatarId: Int? = null
)

class NgepetViewModel(
    private val gamesRepository: GamesRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(NgepetUiState())
    val uiState: StateFlow<NgepetUiState> = _uiState.asStateFlow()

    init {
        checkActiveMatch()
        loadMatches()
        loadTokenBalance()
    }

    // Navigation

    fun goToPhase(phase: NgepetPhase) {
        _uiState.value = _uiState.value.copy(phase = phase, error = null)
        when (phase) {
            NgepetPhase.LOBBY -> loadMatches()
            NgepetPhase.AVATAR_SHOP -> {
                loadAvatarShop()
                loadOwnedAvatars()
            }
            NgepetPhase.LEADERBOARD -> loadLeaderboard(_uiState.value.leaderboardType)
            NgepetPhase.HISTORY -> loadHistory()
            NgepetPhase.RULES -> {}
            else -> {}
        }
    }

    fun goBack() {
        val current = _uiState.value.phase
        val target = when (current) {
            NgepetPhase.MATCH_ROOM -> NgepetPhase.LOBBY
            NgepetPhase.CREATE_MATCH -> NgepetPhase.LOBBY
            NgepetPhase.JOIN_MATCH -> NgepetPhase.LOBBY
            NgepetPhase.AVATAR_SHOP -> NgepetPhase.LOBBY
            NgepetPhase.LEADERBOARD -> NgepetPhase.LOBBY
            NgepetPhase.HISTORY -> NgepetPhase.LOBBY
            NgepetPhase.RULES -> NgepetPhase.LOBBY
            else -> NgepetPhase.LOBBY
        }
        goToPhase(target)
    }

    fun clearError() {
        _uiState.value = _uiState.value.copy(error = null)
    }

    fun clearMessage() {
        _uiState.value = _uiState.value.copy(message = null)
    }

    private fun bumpCounter() = _uiState.value.notificationCounter + 1

    fun clearGuessResult() {
        val currentResult = _uiState.value.guessResult
        val isEnd = currentResult?.isEnd == true
        // Note: lastGuessedIntruderId is only ever set by hostGuess(), so the re-open
        // mechanism (shouldReopenGuess) only fires for the host role. For the intruder
        // path, lastGuessedIntruderId remains null and shouldReopen evaluates to false.
        val shouldReopen = currentResult?.isEnd == false && _uiState.value.lastGuessedIntruderId != null
        _uiState.value = _uiState.value.copy(
            guessResult = null,
            shouldReopenGuess = shouldReopen
        )
        // Auto-redirect to lobby when game has ended
        if (isEnd) {
            resetMatchState()
            goToPhase(NgepetPhase.LOBBY)
        }
    }

    private fun resetMatchState() {
        _uiState.value = _uiState.value.copy(
            currentMatchId = null,
            currentRole = null,
            currentIntruderMatchId = null,
            matchDetail = null,
            activeMatchData = null,
            selectedHiddenItemId = null,
            guessedItemNames = emptySet(),
            intruderGuessCount = 0
        )
    }

    fun clearReopenGuess() {
        _uiState.value = _uiState.value.copy(shouldReopenGuess = false)
    }

    fun showMatchDetail(match: NgepetMatchListItem) {
        _uiState.value = _uiState.value.copy(
            selectedMatchForJoin = match,
            showMatchDetailDialog = true
        )
    }

    fun dismissMatchDetail() {
        _uiState.value = _uiState.value.copy(
            selectedMatchForJoin = null,
            showMatchDetailDialog = false
        )
    }

    // Form state updates

    fun updateCreateHostName(value: String) {
        _uiState.value = _uiState.value.copy(createHostName = value)
    }

    fun updateCreateDifficulty(value: String) {
        _uiState.value = _uiState.value.copy(createDifficulty = value)
    }

    fun updateCreateDuration(value: Int) {
        _uiState.value = _uiState.value.copy(createDuration = value)
    }

    fun updateCreateMaxIntruders(value: Int) {
        _uiState.value = _uiState.value.copy(createMaxIntruders = value)
    }

    fun updateCreateTokenPool(value: String) {
        _uiState.value = _uiState.value.copy(createTokenPool = value)
    }

    fun updateCreateMinToken(value: String) {
        _uiState.value = _uiState.value.copy(createMinToken = value)
    }

    fun updateCreateMaxToken(value: String) {
        _uiState.value = _uiState.value.copy(createMaxToken = value)
    }

    fun updateCreateHouseAvatar(id: Int?) {
        _uiState.value = _uiState.value.copy(createHouseAvatarId = id)
    }

    fun updateJoinName(value: String) {
        _uiState.value = _uiState.value.copy(joinName = value)
    }

    fun updateJoinTokenAmount(value: String) {
        _uiState.value = _uiState.value.copy(joinTokenAmount = value)
    }

    fun updateJoinPlayerAvatar(id: Int?) {
        _uiState.value = _uiState.value.copy(joinPlayerAvatarId = id)
    }

    // Data loading

    fun loadMatches() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.getNgepetMatches()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    matches = response.matches ?: emptyList()
                )
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun loadTokenBalance() {
        viewModelScope.launch {
            try {
                val response = RetrofitInstance.api.getProfile()
                if (response.isSuccessful) {
                    response.body()?.let { profile ->
                        _uiState.value = _uiState.value.copy(tokenBalance = profile.totalToken)
                    }
                }
            } catch (_: Exception) { }
        }
    }

    fun checkActiveMatch() {
        viewModelScope.launch {
            val result = gamesRepository.getNgepetActiveMatch()
            result.onSuccess { response ->
                val data = response.data
                if (data != null && data.matchId != null) {
                    _uiState.value = _uiState.value.copy(
                        activeMatchData = data,
                        currentMatchId = data.matchId,
                        currentRole = data.role,
                        currentIntruderMatchId = data.intruderMatchId,
                        guessedItemNames = emptySet(),
                        intruderGuessCount = 0,
                        selectedHiddenItemId = null
                    )
                    loadMatchDetail(data.matchId)
                    _uiState.value = _uiState.value.copy(phase = NgepetPhase.MATCH_ROOM)
                }
            }.onFailure { /* silently ignore - user may not have active match */ }
        }
    }

    fun loadMatchDetail(matchId: String) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.getNgepetMatchDetail(matchId)
            result.onSuccess { response ->
                val isIntruder = _uiState.value.currentRole == "intruder"
                val maxAttempts = if (isIntruder) {
                    when (response.match?.difficulty?.lowercase()) {
                        "easy" -> 5
                        "medium" -> 4
                        "hard" -> 3
                        else -> 5
                    }
                } else {
                    _uiState.value.maxGuessAttempts
                }
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    matchDetail = response,
                    maxGuessAttempts = maxAttempts,
                    intruderGuessCount = if (isIntruder) 0 else _uiState.value.intruderGuessCount
                )
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun refreshMatchDetail() {
        val matchId = _uiState.value.currentMatchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.getNgepetMatchDetail(matchId)
            result.onSuccess { response ->
                val isIntruder = _uiState.value.currentRole == "intruder"
                val maxAttempts = if (isIntruder) {
                    when (response.match?.difficulty?.lowercase()) {
                        "easy" -> 5
                        "medium" -> 4
                        "hard" -> 3
                        else -> 5
                    }
                } else {
                    _uiState.value.maxGuessAttempts
                }
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    matchDetail = response,
                    maxGuessAttempts = maxAttempts
                )
                // Auto-navigate to lobby if match is closed or intruder status is "end"
                val matchStatus = response.match?.status?.lowercase()
                val currentIntruder = if (isIntruder) {
                    response.match?.intruders?.find { it.id == _uiState.value.currentIntruderMatchId }
                } else null
                if (matchStatus == "closed" || (isIntruder && currentIntruder?.status == "end")) {
                    resetMatchState()
                    goToPhase(NgepetPhase.LOBBY)
                }
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun loadAvatarShop() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.getNgepetAvatarShop()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    avatarShop = response.data ?: emptyList()
                )
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun loadOwnedAvatars() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.getNgepetOwnedAvatars()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    ownedAvatars = response.data ?: emptyList()
                )
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun loadLeaderboard(type: String) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null, leaderboardType = type)
            val result = when (type) {
                "host" -> gamesRepository.getNgepetLeaderboardHost()
                "intruders" -> gamesRepository.getNgepetLeaderboardIntruders()
                else -> gamesRepository.getNgepetLeaderboardHouse()
            }
            result.onSuccess { response ->
                val gson = Gson()
                val dataArray = response.getAsJsonArray("data")
                when (type) {
                    "house" -> {
                        val items: List<NgepetLeaderboardHouseItem> = if (dataArray != null) {
                            gson.fromJson(dataArray, object : TypeToken<List<NgepetLeaderboardHouseItem>>() {}.type)
                        } else emptyList()
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            leaderboardHouse = items,
                            leaderboard = items
                        )
                    }
                    "host" -> {
                        val items: List<NgepetLeaderboardHostItem> = if (dataArray != null) {
                            gson.fromJson(dataArray, object : TypeToken<List<NgepetLeaderboardHostItem>>() {}.type)
                        } else emptyList()
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            leaderboardHost = items,
                            leaderboard = items
                        )
                    }
                    "intruders" -> {
                        val items: List<NgepetLeaderboardIntruderItem> = if (dataArray != null) {
                            gson.fromJson(dataArray, object : TypeToken<List<NgepetLeaderboardIntruderItem>>() {}.type)
                        } else emptyList()
                        _uiState.value = _uiState.value.copy(
                            isLoading = false,
                            leaderboardIntruder = items,
                            leaderboard = items
                        )
                    }
                    else -> {
                        _uiState.value = _uiState.value.copy(isLoading = false)
                    }
                }
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun loadHistory() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.getNgepetHistory()
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    history = response.data ?: emptyList()
                )
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    // Host actions

    fun createMatch() {
        val state = _uiState.value
        if (state.createHostName.isBlank()) {
            _uiState.value = state.copy(error = "Host name is required", notificationCounter = bumpCounter())
            return
        }
        val tokenPool = state.createTokenPool.toIntOrNull()
        if (tokenPool == null || tokenPool <= 0) {
            _uiState.value = state.copy(error = "Token pool must be a positive number", notificationCounter = bumpCounter())
            return
        }

        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val request = NgepetCreateRequest(
                hostName = state.createHostName,
                difficulty = state.createDifficulty,
                guessDurationHours = state.createDuration,
                maxIntruders = state.createMaxIntruders,
                tokenPool = tokenPool,
                minIntruderToken = state.createMinToken.toIntOrNull(),
                maxIntruderToken = state.createMaxToken.toIntOrNull(),
                houseAvatarId = state.createHouseAvatarId
            )
            val result = gamesRepository.createNgepetMatch(request)
            result.onSuccess { response ->
                val matchId = response.id
                if (matchId != null) {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        currentMatchId = matchId,
                        currentRole = "host",
                        message = response.message,
                        notificationCounter = bumpCounter()
                    )
                    loadMatchDetail(matchId)
                    _uiState.value = _uiState.value.copy(phase = NgepetPhase.MATCH_ROOM)
                } else {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        error = "Match created but no ID returned",
                        notificationCounter = bumpCounter()
                    )
                }
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun hideToken(itemName: String) {
        val matchId = _uiState.value.currentMatchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.ngepetStoreHiddenItem(matchId, NgepetHiddenItemRequest(itemName))
            result.onSuccess { message ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = message,
                    notificationCounter = bumpCounter()
                )
                refreshMatchDetail()
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun hostGuess(intruderId: String, itemName: String) {
        val matchId = _uiState.value.currentMatchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null, lastGuessedIntruderId = intruderId)
            val request = NgepetHostGuessRequest(
                matchIntruderId = intruderId,
                itemName = itemName
            )
            val result = gamesRepository.ngepetHostGuess(matchId, request)
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    guessResult = response
                )
                refreshMatchDetail()
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun closeMatch() {
        val matchId = _uiState.value.currentMatchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.closeNgepetMatch(matchId)
            result.onSuccess { response ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = response.message,
                    notificationCounter = bumpCounter(),
                    currentMatchId = null,
                    currentRole = null,
                    currentIntruderMatchId = null,
                    matchDetail = null,
                    activeMatchData = null
                )
                goToPhase(NgepetPhase.LOBBY)
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    // Intruder actions

    fun joinMatch() {
        val state = _uiState.value
        val matchId = state.selectedMatchForJoin?.id ?: return
        if (state.joinName.isBlank()) {
            _uiState.value = state.copy(error = "Name is required", notificationCounter = bumpCounter())
            return
        }
        val tokenAmount = state.joinTokenAmount.toIntOrNull()
        if (tokenAmount == null || tokenAmount <= 0) {
            _uiState.value = state.copy(error = "Token amount must be a positive number", notificationCounter = bumpCounter())
            return
        }
        // Validate token range against match limits
        val minToken = state.selectedMatchForJoin?.minIntruderToken
        val maxToken = state.selectedMatchForJoin?.maxIntruderToken
        if (minToken != null && tokenAmount < minToken) {
            _uiState.value = state.copy(error = "Token minimum is $minToken", notificationCounter = bumpCounter())
            return
        }
        if (maxToken != null && tokenAmount > maxToken) {
            _uiState.value = state.copy(error = "Token maximum is $maxToken", notificationCounter = bumpCounter())
            return
        }

        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val request = NgepetJoinRequest(
                name = state.joinName,
                tokenAmount = tokenAmount,
                avatarId = state.joinPlayerAvatarId
            )
            val result = gamesRepository.joinNgepetMatch(matchId, request)
            result.onSuccess { message ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    currentMatchId = matchId,
                    currentRole = "intruder",
                    message = message,
                    notificationCounter = bumpCounter(),
                    showMatchDetailDialog = false,
                    selectedMatchForJoin = null,
                    intruderGuessCount = 0,
                    guessedItemNames = emptySet(),
                    selectedHiddenItemId = null
                )
                // Load active match to get intruder_match_id
                val activeResult = gamesRepository.getNgepetActiveMatch()
                activeResult.onSuccess { activeResponse ->
                    val data = activeResponse.data
                    if (data != null) {
                        _uiState.value = _uiState.value.copy(
                            currentIntruderMatchId = data.intruderMatchId,
                            activeMatchData = data
                        )
                    }
                }.onFailure {
                    _uiState.value = _uiState.value.copy(
                        error = "Berhasil join, tapi gagal memuat data match. Silakan refresh.",
                        notificationCounter = bumpCounter()
                    )
                }
                loadMatchDetail(matchId)
                _uiState.value = _uiState.value.copy(phase = NgepetPhase.MATCH_ROOM)
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun submitChoice(itemName: String) {
        val matchId = _uiState.value.currentMatchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.submitNgepetChoice(matchId, NgepetSubmitChoiceRequest(itemName))
            result.onSuccess { message ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = message,
                    notificationCounter = bumpCounter()
                )
                refreshMatchDetail()
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun intruderGuessHidden(hiddenItemId: String, itemName: String) {
        val intruderMatchId = _uiState.value.currentIntruderMatchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isGuessing = true, error = null, showGuessItemDialog = false)
            val request = NgepetHiddenGuessRequest(
                hiddenItemId = hiddenItemId,
                matchIntruderId = intruderMatchId,
                itemName = itemName
            )
            val matchId = _uiState.value.currentMatchId ?: return@launch
            val result = gamesRepository.ngepetMakeHiddenGuess(matchId, request)
            result.onSuccess { response ->
                val newGuessedItems = _uiState.value.guessedItemNames + itemName
                val shouldResetSelection = response.isEnd == true || response.isCorrect == true
                _uiState.value = _uiState.value.copy(
                    isGuessing = false,
                    isLoading = false,
                    guessResult = response,
                    intruderGuessCount = _uiState.value.intruderGuessCount + 1,
                    guessedItemNames = newGuessedItems,
                    selectedHiddenItemId = if (shouldResetSelection) null else _uiState.value.selectedHiddenItemId
                )
                refreshMatchDetail()
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isGuessing = false,
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun selectHiddenItem(id: String) {
        _uiState.value = _uiState.value.copy(
            selectedHiddenItemId = id,
            showHiddenItemSelection = false,
            showGuessItemDialog = true
        )
    }

    fun showHiddenItemGrid() {
        _uiState.value = _uiState.value.copy(showHiddenItemSelection = true)
    }

    fun dismissHiddenItemSelection() {
        _uiState.value = _uiState.value.copy(showHiddenItemSelection = false)
    }

    fun dismissGuessItemDialog() {
        _uiState.value = _uiState.value.copy(showGuessItemDialog = false)
    }

    fun claimVictory() {
        val intruderMatchId = _uiState.value.currentIntruderMatchId ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val request = NgepetClaimVictoryRequest(matchIntruderId = intruderMatchId)
            val result = gamesRepository.claimNgepetVictory(request)
            result.onSuccess { message ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = message,
                    notificationCounter = bumpCounter()
                )
                refreshMatchDetail()
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    // Avatar actions

    fun buyAvatar(id: Int) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.buyNgepetAvatar(id)
            result.onSuccess { message ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = message,
                    notificationCounter = bumpCounter()
                )
                loadAvatarShop()
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    fun equipAvatar(id: Int) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            val result = gamesRepository.equipNgepetAvatar(id)
            result.onSuccess { message ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    message = message,
                    notificationCounter = bumpCounter()
                )
                loadOwnedAvatars()
            }.onFailure { e ->
                _uiState.value = _uiState.value.copy(
                    isLoading = false,
                    error = e.message,
                    notificationCounter = bumpCounter()
                )
            }
        }
    }

    class Factory(
        private val gamesRepository: GamesRepository
    ) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            return NgepetViewModel(gamesRepository) as T
        }
    }
}
