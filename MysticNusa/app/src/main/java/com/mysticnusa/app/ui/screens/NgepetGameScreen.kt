package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.History
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.Store
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import androidx.compose.foundation.text.KeyboardOptions
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import coil.compose.AsyncImage
import com.mysticnusa.app.data.models.*
import com.mysticnusa.app.data.repository.GamesRepository
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.components.MysticTextField
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.NgepetPhase
import com.mysticnusa.app.ui.viewmodels.NgepetViewModel

private const val BASE_URL = "https://mystical-nusa.web.id/"

private val DifficultyEasy = Color(0xFF22c55e)
private val DifficultyMedium = Color(0xFFf59e0b)
private val DifficultyHard = Color(0xFFef4444)
private val SuccessColor = Color(0xFF22c55e)

private val TierCommon = Color(0xFF00f0ff)
private val TierUncommon = Color(0xFF1eff00)
private val TierRare = Color(0xFF0070dd)
private val TierMythical = Color(0xFFa335ee)
private val TierLegendary = Color(0xFFff6200)

private fun tierColor(tier: String?): Color {
    return when (tier?.lowercase()) {
        "common" -> TierCommon
        "uncommon" -> TierUncommon
        "rare" -> TierRare
        "mythical" -> TierMythical
        "legendary" -> TierLegendary
        else -> MysticSurface
    }
}

private fun difficultyColor(difficulty: String?): Color {
    return when (difficulty?.lowercase()) {
        "easy" -> DifficultyEasy
        "medium" -> DifficultyMedium
        "hard" -> DifficultyHard
        else -> TextSecondary
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun NgepetGameScreen(navController: NavController) {
    val viewModel: NgepetViewModel = viewModel(
        factory = NgepetViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()

    // Guess result dialog
    if (uiState.guessResult != null) {
        GuessResultDialog(
            guessResult = uiState.guessResult!!,
            onDismiss = {
                val isEnd = uiState.guessResult?.isEnd == true
                viewModel.clearGuessResult()
                if (isEnd) {
                    viewModel.refreshMatchDetail()
                }
            }
        )
    }

    // Message snackbar effect
    uiState.message?.let { msg ->
        LaunchedEffect(msg) {
            kotlinx.coroutines.delay(3000)
            viewModel.clearMessage()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Ngepet Online", color = MysticGold) },
                navigationIcon = {
                    IconButton(onClick = {
                        if (uiState.phase == NgepetPhase.LOBBY) {
                            navController.popBackStack()
                        } else {
                            viewModel.goBack()
                        }
                    }) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, "Back", tint = MysticGold)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MysticDarkBackground
                )
            )
        },
        containerColor = MysticDarkBackground
    ) { paddingValues ->
        Box(
            modifier = Modifier
                .padding(paddingValues)
                .fillMaxSize()
        ) {
            when (uiState.phase) {
                NgepetPhase.LOBBY -> LobbyPhase(viewModel, uiState)
                NgepetPhase.CREATE_MATCH -> CreateMatchPhase(viewModel, uiState)
                NgepetPhase.JOIN_MATCH -> JoinMatchPhase(viewModel, uiState)
                NgepetPhase.MATCH_ROOM -> MatchRoomPhase(viewModel, uiState)
                NgepetPhase.AVATAR_SHOP -> AvatarShopPhase(viewModel, uiState)
                NgepetPhase.LEADERBOARD -> LeaderboardPhase(viewModel, uiState)
                NgepetPhase.HISTORY -> HistoryPhase(viewModel, uiState)
            }

            // Global message display
            uiState.message?.let { msg ->
                Text(
                    text = msg,
                    color = SuccessColor,
                    modifier = Modifier
                        .align(Alignment.BottomCenter)
                        .padding(16.dp)
                        .background(MysticSurface, RoundedCornerShape(8.dp))
                        .padding(horizontal = 16.dp, vertical = 8.dp)
                )
            }
        }
    }
}

// ==================== LOBBY PHASE ====================

@Composable
private fun LobbyPhase(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    // Match detail dialog
    if (uiState.showMatchDetailDialog && uiState.selectedMatchForJoin != null) {
        MatchDetailDialog(
            match = uiState.selectedMatchForJoin,
            onJoin = {
                viewModel.goToPhase(NgepetPhase.JOIN_MATCH)
            },
            onDismiss = { viewModel.dismissMatchDetail() }
        )
    }

    Column(modifier = Modifier.fillMaxSize().padding(16.dp)) {
        // Top toolbar buttons
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(8.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            MysticButton(
                text = "Buka Rumah",
                onClick = { viewModel.goToPhase(NgepetPhase.CREATE_MATCH) },
                modifier = Modifier.weight(1f)
            )
            IconButton(onClick = { viewModel.loadMatches() }) {
                Icon(Icons.Filled.Refresh, "Refresh", tint = MysticGold)
            }
            IconButton(onClick = { viewModel.goToPhase(NgepetPhase.HISTORY) }) {
                Icon(Icons.Filled.History, "History", tint = MysticGold)
            }
            IconButton(onClick = { viewModel.goToPhase(NgepetPhase.LEADERBOARD) }) {
                Icon(Icons.Filled.EmojiEvents, "Leaderboard", tint = MysticGold)
            }
            IconButton(onClick = { viewModel.goToPhase(NgepetPhase.AVATAR_SHOP) }) {
                Icon(Icons.Filled.Store, "Avatar Shop", tint = MysticGold)
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        uiState.error?.let {
            Text(text = it, color = MaterialTheme.colorScheme.error)
            Spacer(modifier = Modifier.height(8.dp))
        }

        if (uiState.isLoading) {
            Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
            }
        } else if (uiState.matches.isEmpty()) {
            Box(
                modifier = Modifier.fillMaxWidth().padding(32.dp),
                contentAlignment = Alignment.Center
            ) {
                Text("Belum ada rumah tersedia", color = TextSecondary)
            }
        } else {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                items(uiState.matches) { match ->
                    LobbyMatchCard(
                        match = match,
                        onClick = { viewModel.showMatchDetail(match) }
                    )
                }
            }
        }
    }
}

@Composable
private fun LobbyMatchCard(match: NgepetLobbyMatch, onClick: () -> Unit) {
    val borderColor = tierColor(match.houseAvatar?.tier)

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .border(2.dp, borderColor, RoundedCornerShape(16.dp))
            .clickable { onClick() },
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = MysticSurface)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // House avatar image
            AsyncImage(
                model = if (match.houseAvatar?.imageUrl != null) BASE_URL + match.houseAvatar.imageUrl else null,
                contentDescription = "House Avatar",
                modifier = Modifier
                    .size(56.dp)
                    .clip(RoundedCornerShape(8.dp)),
                contentScale = ContentScale.Crop
            )

            Spacer(modifier = Modifier.width(12.dp))

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = match.hostName ?: "Unknown Host",
                    color = MysticGold,
                    fontWeight = FontWeight.Bold,
                    fontSize = 16.sp
                )
                Spacer(modifier = Modifier.height(4.dp))
                Row(
                    horizontalArrangement = Arrangement.spacedBy(8.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    // Difficulty badge
                    Text(
                        text = match.difficulty?.uppercase() ?: "",
                        color = difficultyColor(match.difficulty),
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier
                            .background(
                                difficultyColor(match.difficulty).copy(alpha = 0.2f),
                                RoundedCornerShape(4.dp)
                            )
                            .padding(horizontal = 6.dp, vertical = 2.dp)
                    )
                    Text(
                        text = "${match.tokenPool ?: 0} token",
                        color = TextSecondary,
                        fontSize = 12.sp
                    )
                }
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = "Intruders: ${match.intrudersCount ?: 0}/${match.maxIntruders ?: 0}",
                    color = TextSecondary,
                    fontSize = 12.sp
                )
            }
        }
    }
}

@Composable
private fun MatchDetailDialog(
    match: NgepetLobbyMatch,
    onJoin: () -> Unit,
    onDismiss: () -> Unit
) {
    Dialog(onDismissRequest = onDismiss) {
        Card(
            shape = RoundedCornerShape(16.dp),
            colors = CardDefaults.cardColors(containerColor = MysticSurface)
        ) {
            Column(
                modifier = Modifier.padding(20.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                // Avatar image
                AsyncImage(
                    model = if (match.houseAvatar?.imageUrl != null) BASE_URL + match.houseAvatar.imageUrl else null,
                    contentDescription = "House Avatar",
                    modifier = Modifier
                        .size(80.dp)
                        .clip(RoundedCornerShape(12.dp)),
                    contentScale = ContentScale.Crop
                )
                Spacer(modifier = Modifier.height(12.dp))

                Text(
                    text = match.hostName ?: "Unknown Host",
                    color = MysticGold,
                    fontWeight = FontWeight.Bold,
                    fontSize = 18.sp
                )
                Spacer(modifier = Modifier.height(8.dp))

                Text("Token Pool: ${match.tokenPool ?: 0}", color = Color.White)
                Text(
                    "Difficulty: ${match.difficulty ?: "-"}",
                    color = difficultyColor(match.difficulty)
                )
                Text("Duration: ${match.guessDurationHours ?: 0} hours", color = TextSecondary)
                Text("Max Intruders: ${match.maxIntruders ?: 0}", color = TextSecondary)
                if (match.minIntruderToken != null || match.maxIntruderToken != null) {
                    Text(
                        "Token Range: ${match.minIntruderToken ?: "?"} - ${match.maxIntruderToken ?: "?"}",
                        color = TextSecondary
                    )
                }

                Spacer(modifier = Modifier.height(16.dp))

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    OutlinedButton(
                        onClick = onDismiss,
                        modifier = Modifier.weight(1f),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = TextSecondary)
                    ) {
                        Text("Tutup")
                    }
                    Button(
                        onClick = onJoin,
                        modifier = Modifier.weight(1f),
                        colors = ButtonDefaults.buttonColors(
                            containerColor = MysticGold,
                            contentColor = Color.Black
                        )
                    ) {
                        Text("Curi")
                    }
                }
            }
        }
    }
}

// ==================== CREATE MATCH PHASE ====================

@Composable
private fun CreateMatchPhase(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    LaunchedEffect(Unit) {
        viewModel.loadOwnedAvatars()
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp)
    ) {
        Text(
            text = "Buka Rumah Baru",
            color = MysticGold,
            fontSize = 20.sp,
            fontWeight = FontWeight.Bold
        )
        Spacer(modifier = Modifier.height(16.dp))

        // Host name
        MysticTextField(
            value = uiState.createHostName,
            onValueChange = { viewModel.updateCreateHostName(it) },
            label = "Nama Host"
        )
        Spacer(modifier = Modifier.height(12.dp))

        // Token pool
        OutlinedTextField(
            value = uiState.createTokenPool,
            onValueChange = { viewModel.updateCreateTokenPool(it) },
            label = { Text("Token Pool") },
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(12.dp),
            colors = OutlinedTextFieldDefaults.colors(
                focusedBorderColor = MysticGold,
                unfocusedBorderColor = MysticPurple,
                focusedLabelColor = MysticGold,
                unfocusedLabelColor = TextSecondary,
                cursorColor = MysticGold,
                focusedTextColor = Color.White,
                unfocusedTextColor = Color.White
            ),
            singleLine = true,
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number)
        )
        Spacer(modifier = Modifier.height(16.dp))

        // Difficulty selection
        Text("Difficulty", color = TextSecondary, fontSize = 14.sp)
        Spacer(modifier = Modifier.height(8.dp))
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            listOf("easy", "medium", "hard").forEach { diff ->
                FilterChip(
                    selected = uiState.createDifficulty == diff,
                    onClick = { viewModel.updateCreateDifficulty(diff) },
                    label = { Text(diff.replaceFirstChar { it.uppercase() }) },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = difficultyColor(diff).copy(alpha = 0.3f),
                        selectedLabelColor = difficultyColor(diff),
                        labelColor = TextSecondary
                    )
                )
            }
        }
        Spacer(modifier = Modifier.height(16.dp))

        // Duration selection
        Text("Duration (hours)", color = TextSecondary, fontSize = 14.sp)
        Spacer(modifier = Modifier.height(8.dp))
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            listOf(3, 6, 12, 24).forEach { hours ->
                FilterChip(
                    selected = uiState.createDuration == hours,
                    onClick = { viewModel.updateCreateDuration(hours) },
                    label = { Text("${hours}h") },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = MysticPurple.copy(alpha = 0.3f),
                        selectedLabelColor = MysticGold,
                        labelColor = TextSecondary
                    )
                )
            }
        }
        Spacer(modifier = Modifier.height(16.dp))

        // Max intruders
        Text("Max Intruders", color = TextSecondary, fontSize = 14.sp)
        Spacer(modifier = Modifier.height(8.dp))
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            (1..5).forEach { count ->
                FilterChip(
                    selected = uiState.createMaxIntruders == count,
                    onClick = { viewModel.updateCreateMaxIntruders(count) },
                    label = { Text("$count") },
                    colors = FilterChipDefaults.filterChipColors(
                        selectedContainerColor = MysticPurple.copy(alpha = 0.3f),
                        selectedLabelColor = MysticGold,
                        labelColor = TextSecondary
                    )
                )
            }
        }
        Spacer(modifier = Modifier.height(16.dp))

        // Min/max intruder token (optional)
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            OutlinedTextField(
                value = uiState.createMinToken,
                onValueChange = { viewModel.updateCreateMinToken(it) },
                label = { Text("Min Token") },
                modifier = Modifier.weight(1f),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = MysticGold,
                    unfocusedBorderColor = MysticPurple,
                    focusedLabelColor = MysticGold,
                    unfocusedLabelColor = TextSecondary,
                    cursorColor = MysticGold,
                    focusedTextColor = Color.White,
                    unfocusedTextColor = Color.White
                ),
                singleLine = true,
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number)
            )
            OutlinedTextField(
                value = uiState.createMaxToken,
                onValueChange = { viewModel.updateCreateMaxToken(it) },
                label = { Text("Max Token") },
                modifier = Modifier.weight(1f),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = MysticGold,
                    unfocusedBorderColor = MysticPurple,
                    focusedLabelColor = MysticGold,
                    unfocusedLabelColor = TextSecondary,
                    cursorColor = MysticGold,
                    focusedTextColor = Color.White,
                    unfocusedTextColor = Color.White
                ),
                singleLine = true,
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number)
            )
        }
        Spacer(modifier = Modifier.height(16.dp))

        // House avatar selector
        Text("Pilih Avatar Rumah", color = TextSecondary, fontSize = 14.sp)
        Spacer(modifier = Modifier.height(8.dp))
        val houseAvatars = uiState.ownedAvatars.filter { it.avatar?.type == "house" }
        if (houseAvatars.isEmpty()) {
            Text("Belum punya avatar rumah", color = TextSecondary, fontSize = 12.sp)
        } else {
            LazyRow(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                items(houseAvatars) { owned ->
                    val isSelected = uiState.createHouseAvatarId == owned.avatarId
                    Column(
                        modifier = Modifier
                            .clickable { viewModel.updateCreateHouseAvatar(owned.avatarId) }
                            .border(
                                width = if (isSelected) 2.dp else 0.dp,
                                color = if (isSelected) MysticGold else Color.Transparent,
                                shape = RoundedCornerShape(8.dp)
                            )
                            .padding(4.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        AsyncImage(
                            model = if (owned.avatar?.imageUrl != null) BASE_URL + owned.avatar.imageUrl else null,
                            contentDescription = owned.avatar?.name,
                            modifier = Modifier
                                .size(56.dp)
                                .clip(RoundedCornerShape(8.dp)),
                            contentScale = ContentScale.Crop
                        )
                        Text(
                            text = owned.avatar?.name ?: "",
                            color = if (isSelected) MysticGold else TextSecondary,
                            fontSize = 10.sp,
                            maxLines = 1,
                            overflow = TextOverflow.Ellipsis,
                            modifier = Modifier.widthIn(max = 60.dp)
                        )
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(24.dp))

        uiState.error?.let {
            Text(text = it, color = MaterialTheme.colorScheme.error)
            Spacer(modifier = Modifier.height(8.dp))
        }

        if (uiState.isLoading) {
            Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
            }
        } else {
            MysticButton(
                text = "Buka Rumah",
                onClick = { viewModel.createMatch() }
            )
        }
    }
}

// ==================== JOIN MATCH PHASE ====================

@Composable
private fun JoinMatchPhase(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    LaunchedEffect(Unit) {
        viewModel.loadOwnedAvatars()
    }

    val match = uiState.selectedMatchForJoin

    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp)
    ) {
        Text(
            text = "Masuk Rumah",
            color = MysticGold,
            fontSize = 20.sp,
            fontWeight = FontWeight.Bold
        )
        Spacer(modifier = Modifier.height(12.dp))

        // Match info
        if (match != null) {
            Card(
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = MysticSurface)
            ) {
                Column(modifier = Modifier.padding(12.dp)) {
                    Text("Host: ${match.hostName ?: "-"}", color = Color.White)
                    Text("Token Pool: ${match.tokenPool ?: 0}", color = TextSecondary)
                    Text(
                        "Difficulty: ${match.difficulty ?: "-"}",
                        color = difficultyColor(match.difficulty)
                    )
                    if (match.minIntruderToken != null || match.maxIntruderToken != null) {
                        Text(
                            "Token Range: ${match.minIntruderToken ?: "?"} - ${match.maxIntruderToken ?: "?"}",
                            color = TextSecondary
                        )
                    }
                }
            }
            Spacer(modifier = Modifier.height(16.dp))
        }

        // Intruder name
        MysticTextField(
            value = uiState.joinName,
            onValueChange = { viewModel.updateJoinName(it) },
            label = "Nama Intruder"
        )
        Spacer(modifier = Modifier.height(12.dp))

        // Token amount
        OutlinedTextField(
            value = uiState.joinTokenAmount,
            onValueChange = { viewModel.updateJoinTokenAmount(it) },
            label = { Text("Jumlah Token") },
            supportingText = {
                if (match != null && (match.minIntruderToken != null || match.maxIntruderToken != null)) {
                    Text(
                        "Min: ${match.minIntruderToken ?: "?"} - Max: ${match.maxIntruderToken ?: "?"}",
                        color = TextSecondary
                    )
                }
            },
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(12.dp),
            colors = OutlinedTextFieldDefaults.colors(
                focusedBorderColor = MysticGold,
                unfocusedBorderColor = MysticPurple,
                focusedLabelColor = MysticGold,
                unfocusedLabelColor = TextSecondary,
                cursorColor = MysticGold,
                focusedTextColor = Color.White,
                unfocusedTextColor = Color.White
            ),
            singleLine = true,
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number)
        )
        Spacer(modifier = Modifier.height(16.dp))

        // Player avatar selector
        Text("Pilih Avatar Player", color = TextSecondary, fontSize = 14.sp)
        Spacer(modifier = Modifier.height(8.dp))
        val playerAvatars = uiState.ownedAvatars.filter { it.avatar?.type == "player" }
        if (playerAvatars.isEmpty()) {
            Text("Belum punya avatar player", color = TextSecondary, fontSize = 12.sp)
        } else {
            LazyRow(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                items(playerAvatars) { owned ->
                    val isSelected = uiState.joinPlayerAvatarId == owned.avatarId
                    Column(
                        modifier = Modifier
                            .clickable { viewModel.updateJoinPlayerAvatar(owned.avatarId) }
                            .border(
                                width = if (isSelected) 2.dp else 0.dp,
                                color = if (isSelected) MysticGold else Color.Transparent,
                                shape = RoundedCornerShape(8.dp)
                            )
                            .padding(4.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        AsyncImage(
                            model = if (owned.avatar?.imageUrl != null) BASE_URL + owned.avatar.imageUrl else null,
                            contentDescription = owned.avatar?.name,
                            modifier = Modifier
                                .size(56.dp)
                                .clip(RoundedCornerShape(8.dp)),
                            contentScale = ContentScale.Crop
                        )
                        Text(
                            text = owned.avatar?.name ?: "",
                            color = if (isSelected) MysticGold else TextSecondary,
                            fontSize = 10.sp,
                            maxLines = 1,
                            overflow = TextOverflow.Ellipsis,
                            modifier = Modifier.widthIn(max = 60.dp)
                        )
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(24.dp))

        uiState.error?.let {
            Text(text = it, color = MaterialTheme.colorScheme.error)
            Spacer(modifier = Modifier.height(8.dp))
        }

        if (uiState.isLoading) {
            Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
            }
        } else {
            MysticButton(
                text = "Masuk Rumah",
                onClick = { viewModel.joinMatch() }
            )
        }
    }
}

// ==================== MATCH ROOM PHASE ====================

@Composable
private fun MatchRoomPhase(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    val matchDetail = uiState.matchDetail?.match

    if (uiState.isLoading && matchDetail == null) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = MysticGold)
        }
        return
    }

    if (matchDetail == null) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                Text("Tidak ada data match", color = TextSecondary)
                Spacer(modifier = Modifier.height(8.dp))
                MysticButton(text = "Refresh", onClick = { viewModel.refreshMatchDetail() })
            }
        }
        return
    }

    if (uiState.currentRole == "host") {
        HostMatchView(viewModel, uiState, matchDetail)
    } else {
        IntruderMatchView(viewModel, uiState, matchDetail)
    }
}

@Composable
private fun HostMatchView(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState,
    matchDetail: NgepetMatchDetail
) {
    var showHideTokenDialog by remember { mutableStateOf(false) }
    var showGuessDialog by remember { mutableStateOf<String?>(null) } // intruder ID

    // Hide token dialog
    if (showHideTokenDialog) {
        ItemSelectionDialog(
            title = "Sembunyikan Token",
            items = matchDetail.items ?: emptyList(),
            onSelect = { itemName ->
                viewModel.hideToken(itemName)
                showHideTokenDialog = false
            },
            onDismiss = { showHideTokenDialog = false }
        )
    }

    // Guess intruder dialog
    showGuessDialog?.let { intruderId ->
        ItemSelectionDialog(
            title = "Tebak Lokasi Intruder",
            items = matchDetail.items ?: emptyList(),
            onSelect = { itemName ->
                viewModel.hostGuess(intruderId, itemName)
                showGuessDialog = null
            },
            onDismiss = { showGuessDialog = null }
        )
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp)
    ) {
        // Header
        Row(verticalAlignment = Alignment.CenterVertically) {
            AsyncImage(
                model = if (matchDetail.houseAvatar?.imageUrl != null) BASE_URL + matchDetail.houseAvatar.imageUrl else null,
                contentDescription = "House Avatar",
                modifier = Modifier
                    .size(48.dp)
                    .clip(RoundedCornerShape(8.dp)),
                contentScale = ContentScale.Crop
            )
            Spacer(modifier = Modifier.width(12.dp))
            Column {
                Text(
                    text = matchDetail.hostName ?: "Host",
                    color = MysticGold,
                    fontWeight = FontWeight.Bold,
                    fontSize = 18.sp
                )
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text("Pool: ${matchDetail.tokenPool ?: 0}", color = TextSecondary, fontSize = 12.sp)
                    Text(
                        text = matchDetail.difficulty?.uppercase() ?: "",
                        color = difficultyColor(matchDetail.difficulty),
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
                Text(
                    "Intruders: ${matchDetail.intrudersCount ?: 0}/${matchDetail.maxIntruders ?: 0} | Hidden: ${matchDetail.hiddenTokensCount ?: 0}",
                    color = TextSecondary,
                    fontSize = 12.sp
                )
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // Items grid
        Text("Items", color = MysticGold, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.height(8.dp))
        val items = matchDetail.items ?: emptyList()
        items.chunked(2).forEach { row ->
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                row.forEach { item ->
                    Card(
                        modifier = Modifier.weight(1f),
                        shape = RoundedCornerShape(8.dp),
                        colors = CardDefaults.cardColors(containerColor = MysticSurface)
                    ) {
                        Column(
                            modifier = Modifier.padding(8.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            AsyncImage(
                                model = if (item.imageUrl != null) BASE_URL + item.imageUrl else null,
                                contentDescription = item.name,
                                modifier = Modifier
                                    .size(48.dp)
                                    .clip(RoundedCornerShape(4.dp)),
                                contentScale = ContentScale.Crop
                            )
                            Text(
                                text = item.name ?: "",
                                color = TextSecondary,
                                fontSize = 11.sp,
                                maxLines = 1,
                                overflow = TextOverflow.Ellipsis
                            )
                        }
                    }
                }
                if (row.size == 1) Spacer(modifier = Modifier.weight(1f))
            }
            Spacer(modifier = Modifier.height(4.dp))
        }

        Spacer(modifier = Modifier.height(12.dp))

        // Sembunyikan Token button
        Button(
            onClick = { showHideTokenDialog = true },
            colors = ButtonDefaults.buttonColors(
                containerColor = MysticPurple,
                contentColor = Color.White
            ),
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(8.dp)
        ) {
            Text("Sembunyikan Token")
        }

        Spacer(modifier = Modifier.height(16.dp))

        // Intruders section
        Text("Intruders", color = MysticGold, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.height(8.dp))
        val intruders = matchDetail.intruders ?: emptyList()
        if (intruders.isEmpty()) {
            Text("Belum ada intruder", color = TextSecondary, fontSize = 12.sp)
        } else {
            intruders.forEach { intruder ->
                Card(
                    shape = RoundedCornerShape(8.dp),
                    colors = CardDefaults.cardColors(containerColor = MysticSurface),
                    modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)
                ) {
                    Row(
                        modifier = Modifier.padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        AsyncImage(
                            model = if (intruder.avatar?.imageUrl != null) BASE_URL + intruder.avatar.imageUrl else null,
                            contentDescription = intruder.intruderName,
                            modifier = Modifier
                                .size(40.dp)
                                .clip(CircleShape),
                            contentScale = ContentScale.Crop
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        Column(modifier = Modifier.weight(1f)) {
                            Text(
                                text = intruder.intruderName ?: "Unknown",
                                color = Color.White,
                                fontWeight = FontWeight.Bold,
                                fontSize = 14.sp
                            )
                            Text(
                                "Token: ${intruder.tokenPool ?: 0} | Status: ${intruder.status ?: "-"}",
                                color = TextSecondary,
                                fontSize = 11.sp
                            )
                            intruder.result?.let {
                                Text("Result: $it", color = SuccessColor, fontSize = 11.sp)
                            }
                        }
                        if (intruder.status == "wait") {
                            Button(
                                onClick = { showGuessDialog = intruder.id },
                                colors = ButtonDefaults.buttonColors(
                                    containerColor = MysticGold,
                                    contentColor = Color.Black
                                ),
                                contentPadding = PaddingValues(horizontal = 12.dp, vertical = 4.dp)
                            ) {
                                Text("Tebak", fontSize = 12.sp)
                            }
                        }
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // Events log
        EventsLog(matchDetail.events)

        Spacer(modifier = Modifier.height(16.dp))

        // Action buttons
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            Button(
                onClick = { viewModel.refreshMatchDetail() },
                colors = ButtonDefaults.buttonColors(
                    containerColor = MysticPurple,
                    contentColor = Color.White
                ),
                modifier = Modifier.weight(1f),
                shape = RoundedCornerShape(8.dp)
            ) {
                Icon(Icons.Filled.Refresh, contentDescription = null, modifier = Modifier.size(16.dp))
                Spacer(modifier = Modifier.width(4.dp))
                Text("Refresh")
            }
            OutlinedButton(
                onClick = { viewModel.closeMatch() },
                modifier = Modifier.weight(1f),
                colors = ButtonDefaults.outlinedButtonColors(contentColor = DifficultyHard),
                shape = RoundedCornerShape(8.dp)
            ) {
                Text("Tutup Rumah")
            }
        }

        uiState.error?.let {
            Spacer(modifier = Modifier.height(8.dp))
            Text(text = it, color = MaterialTheme.colorScheme.error)
        }
    }
}

@Composable
private fun IntruderMatchView(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState,
    matchDetail: NgepetMatchDetail
) {
    var showGuessHiddenDialog by remember { mutableStateOf(false) }
    var selectedChoiceItem by remember { mutableStateOf<String?>(null) }

    // Guess hidden token dialog
    if (showGuessHiddenDialog) {
        ItemSelectionDialog(
            title = "Tebak Token Host",
            items = matchDetail.items ?: emptyList(),
            onSelect = { itemName ->
                viewModel.intruderGuessHidden(itemName)
                showGuessHiddenDialog = false
            },
            onDismiss = { showGuessHiddenDialog = false }
        )
    }

    // Find current intruder
    val currentIntruder = matchDetail.intruders?.find { it.id == uiState.currentIntruderMatchId }
    val hasPickedChoice = currentIntruder?.isPickChoice != null && currentIntruder.isPickChoice != 0

    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp)
    ) {
        // Header
        Text(
            text = "Rumah ${matchDetail.hostName ?: ""}",
            color = MysticGold,
            fontWeight = FontWeight.Bold,
            fontSize = 18.sp
        )
        Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            Text("Pool: ${matchDetail.tokenPool ?: 0}", color = TextSecondary, fontSize = 12.sp)
            Text(
                text = matchDetail.difficulty?.uppercase() ?: "",
                color = difficultyColor(matchDetail.difficulty),
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold
            )
        }
        Spacer(modifier = Modifier.height(16.dp))

        if (!hasPickedChoice) {
            // Pick hiding spot
            Text(
                "Pilih Tempat Bersembunyi",
                color = MysticGold,
                fontWeight = FontWeight.Bold
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                "Pilih salah satu item untuk menyembunyikan diri",
                color = TextSecondary,
                fontSize = 12.sp
            )
            Spacer(modifier = Modifier.height(12.dp))

            val items = matchDetail.items ?: emptyList()
            items.chunked(2).forEach { row ->
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    row.forEach { item ->
                        val isSelected = selectedChoiceItem == item.name
                        Card(
                            modifier = Modifier
                                .weight(1f)
                                .clickable { selectedChoiceItem = item.name }
                                .then(
                                    if (isSelected) Modifier.border(
                                        2.dp,
                                        MysticGold,
                                        RoundedCornerShape(8.dp)
                                    ) else Modifier
                                ),
                            shape = RoundedCornerShape(8.dp),
                            colors = CardDefaults.cardColors(
                                containerColor = if (isSelected) MysticPurple.copy(alpha = 0.3f) else MysticSurface
                            )
                        ) {
                            Column(
                                modifier = Modifier.padding(8.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                AsyncImage(
                                    model = if (item.imageUrl != null) BASE_URL + item.imageUrl else null,
                                    contentDescription = item.name,
                                    modifier = Modifier
                                        .size(48.dp)
                                        .clip(RoundedCornerShape(4.dp)),
                                    contentScale = ContentScale.Crop
                                )
                                Text(
                                    text = item.name ?: "",
                                    color = if (isSelected) MysticGold else TextSecondary,
                                    fontSize = 11.sp,
                                    maxLines = 1,
                                    overflow = TextOverflow.Ellipsis
                                )
                            }
                        }
                    }
                    if (row.size == 1) Spacer(modifier = Modifier.weight(1f))
                }
                Spacer(modifier = Modifier.height(4.dp))
            }

            Spacer(modifier = Modifier.height(12.dp))

            if (selectedChoiceItem != null) {
                MysticButton(
                    text = "Pilih Tempat Sembunyi",
                    onClick = {
                        selectedChoiceItem?.let { viewModel.submitChoice(it) }
                    }
                )
            }
        } else {
            // Already picked - waiting state
            Card(
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = MysticSurface)
            ) {
                Column(
                    modifier = Modifier.padding(16.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Text(
                        "Kamu sudah bersembunyi!",
                        color = SuccessColor,
                        fontWeight = FontWeight.Bold
                    )
                    currentIntruder?.guessDeadline?.let {
                        Spacer(modifier = Modifier.height(4.dp))
                        Text("Deadline: $it", color = TextSecondary, fontSize = 12.sp)
                    }
                    currentIntruder?.status?.let {
                        Spacer(modifier = Modifier.height(4.dp))
                        Text("Status: $it", color = TextSecondary, fontSize = 12.sp)
                    }
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            // Guess hidden token button
            Button(
                onClick = { showGuessHiddenDialog = true },
                colors = ButtonDefaults.buttonColors(
                    containerColor = MysticPurple,
                    contentColor = Color.White
                ),
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(8.dp)
            ) {
                Text("Tebak Token Host")
            }

            Spacer(modifier = Modifier.height(8.dp))

            // Claim victory button
            OutlinedButton(
                onClick = { viewModel.claimVictory() },
                modifier = Modifier.fillMaxWidth(),
                colors = ButtonDefaults.outlinedButtonColors(contentColor = MysticGold),
                shape = RoundedCornerShape(8.dp)
            ) {
                Text("Klaim Kemenangan")
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // Events log
        EventsLog(matchDetail.events)

        Spacer(modifier = Modifier.height(16.dp))

        // Refresh button
        Button(
            onClick = { viewModel.refreshMatchDetail() },
            colors = ButtonDefaults.buttonColors(
                containerColor = MysticPurple,
                contentColor = Color.White
            ),
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(8.dp)
        ) {
            Icon(Icons.Filled.Refresh, contentDescription = null, modifier = Modifier.size(16.dp))
            Spacer(modifier = Modifier.width(4.dp))
            Text("Refresh")
        }

        uiState.error?.let {
            Spacer(modifier = Modifier.height(8.dp))
            Text(text = it, color = MaterialTheme.colorScheme.error)
        }
    }
}

@Composable
private fun ItemSelectionDialog(
    title: String,
    items: List<NgepetMatchItem>,
    onSelect: (String) -> Unit,
    onDismiss: () -> Unit
) {
    Dialog(onDismissRequest = onDismiss) {
        Card(
            shape = RoundedCornerShape(16.dp),
            colors = CardDefaults.cardColors(containerColor = MysticSurface)
        ) {
            Column(modifier = Modifier.padding(16.dp)) {
                Text(title, color = MysticGold, fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Spacer(modifier = Modifier.height(12.dp))

                items.chunked(2).forEach { row ->
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        row.forEach { item ->
                            Card(
                                modifier = Modifier
                                    .weight(1f)
                                    .clickable { item.name?.let { onSelect(it) } },
                                shape = RoundedCornerShape(8.dp),
                                colors = CardDefaults.cardColors(containerColor = MysticDarkBackground)
                            ) {
                                Column(
                                    modifier = Modifier.padding(8.dp),
                                    horizontalAlignment = Alignment.CenterHorizontally
                                ) {
                                    AsyncImage(
                                        model = if (item.imageUrl != null) BASE_URL + item.imageUrl else null,
                                        contentDescription = item.name,
                                        modifier = Modifier
                                            .size(40.dp)
                                            .clip(RoundedCornerShape(4.dp)),
                                        contentScale = ContentScale.Crop
                                    )
                                    Text(
                                        text = item.name ?: "",
                                        color = TextSecondary,
                                        fontSize = 10.sp,
                                        maxLines = 1,
                                        overflow = TextOverflow.Ellipsis
                                    )
                                }
                            }
                        }
                        if (row.size == 1) Spacer(modifier = Modifier.weight(1f))
                    }
                    Spacer(modifier = Modifier.height(4.dp))
                }

                Spacer(modifier = Modifier.height(12.dp))
                OutlinedButton(
                    onClick = onDismiss,
                    modifier = Modifier.fillMaxWidth(),
                    colors = ButtonDefaults.outlinedButtonColors(contentColor = TextSecondary)
                ) {
                    Text("Batal")
                }
            }
        }
    }
}

@Composable
private fun EventsLog(events: List<NgepetEvent>?) {
    if (events.isNullOrEmpty()) return

    Text("Events", color = MysticGold, fontWeight = FontWeight.Bold)
    Spacer(modifier = Modifier.height(4.dp))
    Card(
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = MysticSurface)
    ) {
        Column(modifier = Modifier.padding(8.dp)) {
            events.takeLast(10).reversed().forEach { event ->
                Row(modifier = Modifier.padding(vertical = 2.dp)) {
                    Text(
                        text = "[${event.role ?: "?"}]",
                        color = MysticPurpleLight,
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Bold
                    )
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(
                        text = event.details ?: "",
                        color = TextSecondary,
                        fontSize = 11.sp,
                        modifier = Modifier.weight(1f)
                    )
                    Text(
                        text = event.createdAt?.takeLast(8) ?: "",
                        color = TextSecondary.copy(alpha = 0.6f),
                        fontSize = 9.sp
                    )
                }
            }
        }
    }
}

// ==================== AVATAR SHOP PHASE ====================

@Composable
private fun AvatarShopPhase(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    var selectedTab by remember { mutableIntStateOf(0) }
    val tabs = listOf("Toko", "Koleksi")

    LaunchedEffect(Unit) {
        viewModel.loadAvatarShop()
        viewModel.loadOwnedAvatars()
    }

    Column(modifier = Modifier.fillMaxSize()) {
        TabRow(
            selectedTabIndex = selectedTab,
            containerColor = MysticDarkBackground,
            contentColor = MysticGold
        ) {
            tabs.forEachIndexed { index, title ->
                Tab(
                    selected = selectedTab == index,
                    onClick = { selectedTab = index },
                    text = {
                        Text(
                            title,
                            color = if (selectedTab == index) MysticGold else TextSecondary
                        )
                    }
                )
            }
        }

        if (uiState.isLoading) {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = MysticGold)
            }
        } else {
            when (selectedTab) {
                0 -> AvatarShopTab(viewModel, uiState)
                1 -> AvatarCollectionTab(viewModel, uiState)
            }
        }

        uiState.error?.let {
            Text(
                text = it,
                color = MaterialTheme.colorScheme.error,
                modifier = Modifier.padding(16.dp)
            )
        }
    }
}

@Composable
private fun AvatarShopTab(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    val avatars = uiState.avatarShop
    if (avatars.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text("Toko kosong", color = TextSecondary)
        }
        return
    }

    LazyColumn(
        modifier = Modifier.fillMaxSize().padding(8.dp),
        verticalArrangement = Arrangement.spacedBy(8.dp)
    ) {
        items(avatars.chunked(2)) { row ->
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                row.forEach { avatar ->
                    Card(
                        modifier = Modifier.weight(1f),
                        shape = RoundedCornerShape(12.dp),
                        colors = CardDefaults.cardColors(containerColor = MysticSurface)
                    ) {
                        Column(
                            modifier = Modifier.padding(8.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            AsyncImage(
                                model = if (avatar.imageUrl != null) BASE_URL + avatar.imageUrl else null,
                                contentDescription = avatar.name,
                                modifier = Modifier
                                    .size(64.dp)
                                    .clip(RoundedCornerShape(8.dp))
                                    .border(1.dp, tierColor(avatar.tier), RoundedCornerShape(8.dp)),
                                contentScale = ContentScale.Crop
                            )
                            Spacer(modifier = Modifier.height(4.dp))
                            Text(
                                text = avatar.name ?: "",
                                color = Color.White,
                                fontSize = 12.sp,
                                fontWeight = FontWeight.Bold,
                                maxLines = 1,
                                overflow = TextOverflow.Ellipsis
                            )
                            // Tier badge
                            Text(
                                text = avatar.tier?.uppercase() ?: "",
                                color = tierColor(avatar.tier),
                                fontSize = 10.sp,
                                fontWeight = FontWeight.Bold
                            )
                            Text(
                                text = "Price: ${avatar.price ?: 0}",
                                color = TextSecondary,
                                fontSize = 10.sp
                            )
                            Text(
                                text = "Stock: ${avatar.stock ?: 0}",
                                color = TextSecondary,
                                fontSize = 10.sp
                            )
                            Spacer(modifier = Modifier.height(4.dp))
                            if (avatar.own == 1) {
                                Text(
                                    text = "Dimiliki",
                                    color = SuccessColor,
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.Bold
                                )
                            } else {
                                Button(
                                    onClick = { viewModel.buyAvatar(avatar.id) },
                                    colors = ButtonDefaults.buttonColors(
                                        containerColor = MysticGold,
                                        contentColor = Color.Black
                                    ),
                                    contentPadding = PaddingValues(horizontal = 12.dp, vertical = 2.dp),
                                    modifier = Modifier.height(28.dp)
                                ) {
                                    Text("Beli", fontSize = 11.sp)
                                }
                            }
                        }
                    }
                }
                if (row.size == 1) Spacer(modifier = Modifier.weight(1f))
            }
        }
    }
}

@Composable
private fun AvatarCollectionTab(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    val owned = uiState.ownedAvatars
    if (owned.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text("Belum punya avatar", color = TextSecondary)
        }
        return
    }

    LazyColumn(
        modifier = Modifier.fillMaxSize().padding(8.dp),
        verticalArrangement = Arrangement.spacedBy(8.dp)
    ) {
        items(owned) { ownedItem ->
            Card(
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = MysticSurface),
                modifier = Modifier.fillMaxWidth()
            ) {
                Row(
                    modifier = Modifier.padding(12.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    AsyncImage(
                        model = if (ownedItem.avatar?.imageUrl != null) BASE_URL + ownedItem.avatar.imageUrl else null,
                        contentDescription = ownedItem.avatar?.name,
                        modifier = Modifier
                            .size(48.dp)
                            .clip(RoundedCornerShape(8.dp))
                            .border(1.dp, tierColor(ownedItem.avatar?.tier), RoundedCornerShape(8.dp)),
                        contentScale = ContentScale.Crop
                    )
                    Spacer(modifier = Modifier.width(12.dp))
                    Column(modifier = Modifier.weight(1f)) {
                        Text(
                            text = ownedItem.avatar?.name ?: "",
                            color = Color.White,
                            fontWeight = FontWeight.Bold,
                            fontSize = 14.sp
                        )
                        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                            Text(
                                text = ownedItem.avatar?.tier?.uppercase() ?: "",
                                color = tierColor(ownedItem.avatar?.tier),
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold
                            )
                            ownedItem.avatar?.type?.let { type ->
                                Text(
                                    text = type.uppercase(),
                                    color = if (type == "house") MysticGold else MysticPurpleLight,
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.Bold,
                                    modifier = Modifier
                                        .background(
                                            (if (type == "house") MysticGold else MysticPurpleLight).copy(alpha = 0.2f),
                                            RoundedCornerShape(4.dp)
                                        )
                                        .padding(horizontal = 4.dp, vertical = 1.dp)
                                )
                            }
                        }
                    }
                    if (ownedItem.isEquipped == true) {
                        Text(
                            text = "Dipakai",
                            color = SuccessColor,
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Bold
                        )
                    } else {
                        Button(
                            onClick = { viewModel.equipAvatar(ownedItem.id) },
                            colors = ButtonDefaults.buttonColors(
                                containerColor = MysticPurple,
                                contentColor = Color.White
                            ),
                            contentPadding = PaddingValues(horizontal = 12.dp, vertical = 2.dp),
                            modifier = Modifier.height(28.dp)
                        ) {
                            Text("Pakai", fontSize = 11.sp)
                        }
                    }
                }
            }
        }
    }
}

// ==================== LEADERBOARD PHASE ====================

@Composable
private fun LeaderboardPhase(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    val tabs = listOf("Rumah", "Host", "Babi")
    val types = listOf("house", "host", "intruders")
    val selectedIndex = types.indexOf(uiState.leaderboardType).coerceAtLeast(0)

    Column(modifier = Modifier.fillMaxSize()) {
        TabRow(
            selectedTabIndex = selectedIndex,
            containerColor = MysticDarkBackground,
            contentColor = MysticGold
        ) {
            tabs.forEachIndexed { index, title ->
                Tab(
                    selected = selectedIndex == index,
                    onClick = { viewModel.loadLeaderboard(types[index]) },
                    text = {
                        Text(
                            title,
                            color = if (selectedIndex == index) MysticGold else TextSecondary
                        )
                    }
                )
            }
        }

        if (uiState.isLoading) {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = MysticGold)
            }
        } else if (uiState.leaderboard.isEmpty()) {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Text("Belum ada data", color = TextSecondary)
            }
        } else {
            LazyColumn(
                modifier = Modifier.fillMaxSize().padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                items(uiState.leaderboard.size) { index ->
                    val entry = uiState.leaderboard[index]
                    Card(
                        shape = RoundedCornerShape(8.dp),
                        colors = CardDefaults.cardColors(containerColor = MysticSurface),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            modifier = Modifier.padding(12.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            // Rank
                            Text(
                                text = "#${index + 1}",
                                color = MysticGold,
                                fontWeight = FontWeight.Bold,
                                fontSize = 16.sp,
                                modifier = Modifier.width(36.dp)
                            )
                            Column(modifier = Modifier.weight(1f)) {
                                Text(
                                    text = entry.name ?: entry.hostName ?: "-",
                                    color = Color.White,
                                    fontWeight = FontWeight.Bold,
                                    fontSize = 14.sp
                                )
                                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                    entry.wins?.let {
                                        Text("Wins: $it", color = TextSecondary, fontSize = 11.sp)
                                    }
                                    entry.totalMatches?.let {
                                        Text("Matches: $it", color = TextSecondary, fontSize = 11.sp)
                                    }
                                    entry.tokenPool?.let {
                                        Text("Pool: $it", color = TextSecondary, fontSize = 11.sp)
                                    }
                                    entry.winrate?.let {
                                        Text(
                                            "WR: ${"%.1f".format(it)}%",
                                            color = SuccessColor,
                                            fontSize = 11.sp
                                        )
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        uiState.error?.let {
            Text(
                text = it,
                color = MaterialTheme.colorScheme.error,
                modifier = Modifier.padding(16.dp)
            )
        }
    }
}

// ==================== HISTORY PHASE ====================

@Composable
private fun HistoryPhase(
    viewModel: NgepetViewModel,
    uiState: com.mysticnusa.app.ui.viewmodels.NgepetUiState
) {
    LaunchedEffect(Unit) {
        viewModel.loadHistory()
    }

    Column(modifier = Modifier.fillMaxSize()) {
        Text(
            text = "Riwayat Match",
            color = MysticGold,
            fontSize = 20.sp,
            fontWeight = FontWeight.Bold,
            modifier = Modifier.padding(16.dp)
        )

        if (uiState.isLoading) {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = MysticGold)
            }
        } else if (uiState.history.isEmpty()) {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Text("Belum ada riwayat", color = TextSecondary)
            }
        } else {
            LazyColumn(
                modifier = Modifier.fillMaxSize().padding(horizontal = 16.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                items(uiState.history) { item ->
                    Card(
                        shape = RoundedCornerShape(8.dp),
                        colors = CardDefaults.cardColors(containerColor = MysticSurface),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            modifier = Modifier.padding(12.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Column(modifier = Modifier.weight(1f)) {
                                Text(
                                    text = item.hostName ?: "-",
                                    color = Color.White,
                                    fontWeight = FontWeight.Bold,
                                    fontSize = 14.sp
                                )
                                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                    // Role badge
                                    item.role?.let { role ->
                                        val roleColor = if (role == "host") MysticGold else MysticPurpleLight
                                        Text(
                                            text = role.uppercase(),
                                            color = roleColor,
                                            fontSize = 10.sp,
                                            fontWeight = FontWeight.Bold,
                                            modifier = Modifier
                                                .background(
                                                    roleColor.copy(alpha = 0.2f),
                                                    RoundedCornerShape(4.dp)
                                                )
                                                .padding(horizontal = 4.dp, vertical = 1.dp)
                                        )
                                    }
                                    item.status?.let {
                                        Text(text = it, color = TextSecondary, fontSize = 11.sp)
                                    }
                                }
                                item.matchResult?.let {
                                    Text(
                                        text = "Result: $it",
                                        color = SuccessColor,
                                        fontSize = 11.sp
                                    )
                                }
                            }
                            item.createdAt?.let {
                                Text(
                                    text = it.take(10),
                                    color = TextSecondary.copy(alpha = 0.7f),
                                    fontSize = 10.sp
                                )
                            }
                        }
                    }
                }
            }
        }

        uiState.error?.let {
            Text(
                text = it,
                color = MaterialTheme.colorScheme.error,
                modifier = Modifier.padding(16.dp)
            )
        }
    }
}

// ==================== GUESS RESULT DIALOG ====================

@Composable
private fun GuessResultDialog(
    guessResult: NgepetGuessResponse,
    onDismiss: () -> Unit
) {
    Dialog(onDismissRequest = onDismiss) {
        Card(
            shape = RoundedCornerShape(16.dp),
            colors = CardDefaults.cardColors(containerColor = MysticSurface)
        ) {
            Column(
                modifier = Modifier.padding(24.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                // Result icon/text
                if (guessResult.isCorrect == true) {
                    Text(
                        text = "Tebakan Benar!",
                        color = SuccessColor,
                        fontWeight = FontWeight.Bold,
                        fontSize = 20.sp
                    )
                } else {
                    Text(
                        text = "Tebakan Salah!",
                        color = DifficultyHard,
                        fontWeight = FontWeight.Bold,
                        fontSize = 20.sp
                    )
                }

                Spacer(modifier = Modifier.height(12.dp))

                if (guessResult.isEnd == true) {
                    Text(
                        text = "Match Selesai",
                        color = MysticGold,
                        fontWeight = FontWeight.Bold,
                        fontSize = 16.sp
                    )
                    guessResult.answerItem?.let {
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = "Jawaban: $it",
                            color = TextSecondary
                        )
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                Button(
                    onClick = onDismiss,
                    colors = ButtonDefaults.buttonColors(
                        containerColor = MysticGold,
                        contentColor = Color.Black
                    ),
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(8.dp)
                ) {
                    Text("OK")
                }
            }
        }
    }
}
