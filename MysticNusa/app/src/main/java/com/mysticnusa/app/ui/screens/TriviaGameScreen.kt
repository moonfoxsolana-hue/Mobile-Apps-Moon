package com.mysticnusa.app.ui.screens

import androidx.compose.animation.core.*
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Close
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.repository.GamesRepository
import com.mysticnusa.app.ui.components.GameBackground
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticTextField
import com.mysticnusa.app.ui.components.SoundManager
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.TriviaRoomPhase
import com.mysticnusa.app.ui.viewmodels.TriviaViewModel
import kotlinx.coroutines.delay

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TriviaGameScreen(navController: NavController) {
    val viewModel: TriviaViewModel = viewModel(
        factory = TriviaViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()
    val context = LocalContext.current

    var category by remember { mutableStateOf("") }
    var questionCount by remember { mutableStateOf(10f) }
    var selectedAnswer by remember { mutableStateOf<String?>(null) }

    // Room multiplayer state
    var showCreateRoomDialog by remember { mutableStateOf(false) }
    var showJoinRoomDialog by remember { mutableStateOf(false) }
    var joinRoomId by remember { mutableIntStateOf(0) }
    var joinRoomCode by remember { mutableStateOf("") }
    var createRoomName by remember { mutableStateOf("") }
    var createRoomCategory by remember { mutableStateOf("") }
    var createRoomMaxPlayers by remember { mutableStateOf(4f) }
    var createRoomCode by remember { mutableStateOf("") }
    var createRoomLogicMode by remember { mutableStateOf(false) }
    var roomSelectedAnswer by remember { mutableStateOf<String?>(null) }

    // BGM lifecycle
    LaunchedEffect(Unit) {
        SoundManager.playBgm(context, "https://mystical-nusa.web.id/sound/bgm/bgm-iq.mp3")
    }
    DisposableEffect(Unit) {
        onDispose {
            SoundManager.stopBgm()
        }
    }

    // Sound effects for correct/wrong answers
    LaunchedEffect(uiState.showFeedback, uiState.lastAnswerCorrect) {
        if (uiState.showFeedback) {
            when (uiState.lastAnswerCorrect) {
                true -> {
                    if (uiState.streak >= 5) {
                        SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/great-success.mp3")
                    } else {
                        SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/success.mp3")
                    }
                }
                false -> SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/wrong.mp3")
                else -> { /* no-op */ }
            }
        }
    }

    // Sound effect for game finish
    LaunchedEffect(uiState.finishResponse) {
        if (uiState.finishResponse != null) {
            SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/finish.mp3")
        }
    }

    Box(modifier = Modifier.fillMaxSize()) {
        // Background layer
        GameBackground(
            imageUrl = "https://mystical-nusa.web.id/images/asset/games/background/trivia-background.jpg"
        )

        // Content layer
        Scaffold(
            topBar = {
                TopAppBar(
                    title = { Text("Trivia", color = TriviaCyan) },
                    navigationIcon = {
                        IconButton(onClick = { navController.popBackStack() }) {
                            Icon(Icons.AutoMirrored.Filled.ArrowBack, "Back", tint = TriviaCyan)
                        }
                    },
                    actions = {
                        IconButton(onClick = { viewModel.toggleStats() }) {
                            Text("\uD83D\uDCCA", fontSize = 20.sp)
                        }
                        IconButton(onClick = { viewModel.toggleLeaderboard() }) {
                            Text("\uD83C\uDFC6", fontSize = 20.sp)
                        }
                    },
                    colors = TopAppBarDefaults.topAppBarColors(
                        containerColor = Color.Transparent
                    )
                )
            },
            containerColor = Color.Transparent
        ) { paddingValues ->
            Box(modifier = Modifier.padding(paddingValues).fillMaxSize()) {
                when {
                    // Room List Phase
                    uiState.roomPhase == TriviaRoomPhase.ROOM_LIST -> {
                        Column(
                            modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(16.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text("Room Multiplayer", color = TriviaCyan, fontSize = 24.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(8.dp))
                            Text("Pilih room atau buat baru", color = TextSecondary, style = MaterialTheme.typography.bodyMedium)
                            Spacer(modifier = Modifier.height(16.dp))

                            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                MysticButton(
                                    text = "Buat Room",
                                    onClick = { showCreateRoomDialog = true },
                                    modifier = Modifier.weight(1f)
                                )
                                MysticButton(
                                    text = "Kembali",
                                    onClick = { viewModel.exitRoomMode() },
                                    modifier = Modifier.weight(1f)
                                )
                            }

                            Spacer(modifier = Modifier.height(16.dp))

                            if (uiState.roomLoading) {
                                CircularProgressIndicator(color = TriviaCyan)
                            } else if (uiState.rooms.isEmpty()) {
                                Text("Belum ada room tersedia", color = TextSecondary)
                            } else {
                                uiState.rooms.forEach { room ->
                                    Card(
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .padding(vertical = 4.dp)
                                            .border(1.dp, TriviaCyan.copy(alpha = 0.3f), RoundedCornerShape(12.dp))
                                            .clickable {
                                                joinRoomId = room.id
                                                joinRoomCode = ""
                                                showJoinRoomDialog = true
                                            },
                                        shape = RoundedCornerShape(12.dp),
                                        colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                                    ) {
                                        Column(modifier = Modifier.padding(16.dp)) {
                                            Row(
                                                modifier = Modifier.fillMaxWidth(),
                                                horizontalArrangement = Arrangement.SpaceBetween,
                                                verticalAlignment = Alignment.CenterVertically
                                            ) {
                                                Text(
                                                    text = room.name ?: "Room #${room.id}",
                                                    color = Color.White,
                                                    fontWeight = FontWeight.Medium,
                                                    fontSize = 16.sp
                                                )
                                                Surface(
                                                    shape = RoundedCornerShape(8.dp),
                                                    color = TriviaCyan.copy(alpha = 0.15f)
                                                ) {
                                                    Text(
                                                        text = "${room.playersCount ?: room.players?.size ?: 0}/${room.maxPlayers ?: "?"}",
                                                        color = TriviaCyan,
                                                        fontSize = 12.sp,
                                                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
                                                    )
                                                }
                                            }
                                            Spacer(modifier = Modifier.height(4.dp))
                                            Text(
                                                text = "Kategori: ${room.category ?: "Random"} | Status: ${room.status ?: "waiting"}",
                                                color = TextSecondary,
                                                style = MaterialTheme.typography.bodySmall
                                            )
                                        }
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(16.dp))
                            MysticButton(text = "Refresh", onClick = { viewModel.loadRoomList() })

                            uiState.error?.let {
                                Spacer(modifier = Modifier.height(12.dp))
                                Card(shape = RoundedCornerShape(8.dp), colors = CardDefaults.cardColors(containerColor = Color(0xFFef4444).copy(alpha = 0.1f))) {
                                    Text(text = it, color = Color(0xFFef4444), style = MaterialTheme.typography.bodySmall, modifier = Modifier.padding(12.dp))
                                }
                            }
                        }

                        // Create Room Dialog
                        if (showCreateRoomDialog) {
                            AlertDialog(
                                onDismissRequest = { showCreateRoomDialog = false },
                                title = { Text("Buat Room Baru", color = TriviaCyan) },
                                containerColor = MysticDarkOverlay,
                                text = {
                                    Column {
                                        MysticTextField(value = createRoomName, onValueChange = { createRoomName = it }, label = "Nama Room")
                                        Spacer(modifier = Modifier.height(8.dp))
                                        MysticTextField(value = createRoomCategory, onValueChange = { createRoomCategory = it }, label = "Kategori (contoh: sains)")
                                        Spacer(modifier = Modifier.height(8.dp))
                                        Text("Max Pemain: ${createRoomMaxPlayers.toInt()}", color = TextSecondary)
                                        Slider(
                                            value = createRoomMaxPlayers, onValueChange = { createRoomMaxPlayers = it },
                                            valueRange = 2f..8f, steps = 5,
                                            colors = SliderDefaults.colors(thumbColor = TriviaCyan, activeTrackColor = TriviaCyan)
                                        )
                                        MysticTextField(value = createRoomCode, onValueChange = { createRoomCode = it }, label = "Kode Join (opsional)")
                                        Spacer(modifier = Modifier.height(8.dp))
                                        Row(verticalAlignment = Alignment.CenterVertically) {
                                            Checkbox(
                                                checked = createRoomLogicMode,
                                                onCheckedChange = { createRoomLogicMode = it },
                                                colors = CheckboxDefaults.colors(checkedColor = TriviaCyan)
                                            )
                                            Spacer(modifier = Modifier.width(4.dp))
                                            Text("Logic Mode", color = TextSecondary)
                                        }
                                    }
                                },
                                confirmButton = {
                                    MysticButton(
                                        text = "Buat",
                                        onClick = {
                                            showCreateRoomDialog = false
                                            viewModel.createRoom(
                                                name = createRoomName.ifBlank { "Room" },
                                                category = createRoomCategory.ifBlank { "general" },
                                                maxPlayers = createRoomMaxPlayers.toInt(),
                                                joinCode = createRoomCode.ifBlank { null },
                                                logicMode = if (createRoomLogicMode) true else null
                                            )
                                        }
                                    )
                                },
                                dismissButton = {
                                    TextButton(onClick = { showCreateRoomDialog = false }) {
                                        Text("Batal", color = TextSecondary)
                                    }
                                }
                            )
                        }

                        // Join Room Dialog
                        if (showJoinRoomDialog) {
                            AlertDialog(
                                onDismissRequest = { showJoinRoomDialog = false },
                                title = { Text("Gabung Room", color = TriviaCyan) },
                                containerColor = MysticDarkOverlay,
                                text = {
                                    Column {
                                        Text("Masukkan kode jika diperlukan:", color = TextSecondary)
                                        Spacer(modifier = Modifier.height(8.dp))
                                        MysticTextField(value = joinRoomCode, onValueChange = { joinRoomCode = it }, label = "Kode Room (opsional)")
                                    }
                                },
                                confirmButton = {
                                    MysticButton(
                                        text = "Gabung",
                                        onClick = {
                                            showJoinRoomDialog = false
                                            viewModel.joinRoom(joinRoomId, joinRoomCode.ifBlank { null })
                                        }
                                    )
                                },
                                dismissButton = {
                                    TextButton(onClick = { showJoinRoomDialog = false }) {
                                        Text("Batal", color = TextSecondary)
                                    }
                                }
                            )
                        }
                    }

                    // Room Lobby Phase
                    uiState.roomPhase == TriviaRoomPhase.ROOM_LOBBY -> {
                        val room = uiState.currentRoom
                        Column(
                            modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(16.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text("Lobby: ${room?.name ?: "Room"}", color = TriviaCyan, fontSize = 22.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(4.dp))
                            Text("Kategori: ${room?.category ?: "Random"}", color = TextSecondary)
                            room?.joinCode?.let { code ->
                                Spacer(modifier = Modifier.height(4.dp))
                                Text("Kode: $code", color = TriviaCyan.copy(alpha = 0.7f), fontSize = 12.sp)
                            }
                            Spacer(modifier = Modifier.height(16.dp))

                            // Players list
                            Text("Pemain (${room?.players?.size ?: 0}/${room?.maxPlayers ?: "?"})", color = TextSecondary, fontWeight = FontWeight.Medium)
                            Spacer(modifier = Modifier.height(8.dp))

                            room?.players?.forEach { playerInfo ->
                                Card(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .padding(vertical = 4.dp)
                                        .border(1.dp, if (playerInfo.isReady == 1) Color(0xFF22c55e).copy(alpha = 0.5f) else TriviaCyan.copy(alpha = 0.2f), RoundedCornerShape(12.dp)),
                                    shape = RoundedCornerShape(12.dp),
                                    colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                                ) {
                                    Row(
                                        modifier = Modifier.padding(12.dp).fillMaxWidth(),
                                        verticalAlignment = Alignment.CenterVertically,
                                        horizontalArrangement = Arrangement.SpaceBetween
                                    ) {
                                        Row(verticalAlignment = Alignment.CenterVertically) {
                                            Text(
                                                text = playerInfo.player?.name ?: "Player #${playerInfo.playerId}",
                                                color = Color.White
                                            )
                                            if (playerInfo.isHost == 1) {
                                                Spacer(modifier = Modifier.width(8.dp))
                                                Surface(shape = RoundedCornerShape(4.dp), color = TriviaCyan.copy(alpha = 0.2f)) {
                                                    Text("HOST", color = TriviaCyan, fontSize = 10.sp, modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp))
                                                }
                                            }
                                        }
                                        Row(verticalAlignment = Alignment.CenterVertically) {
                                            Text(
                                                text = if (playerInfo.isReady == 1) "Ready" else "Not Ready",
                                                color = if (playerInfo.isReady == 1) Color(0xFF22c55e) else Color(0xFFef4444),
                                                fontSize = 12.sp
                                            )
                                            // Kick button (host only, not self)
                                            if (uiState.isHost && playerInfo.playerId != uiState.playerId) {
                                                Spacer(modifier = Modifier.width(8.dp))
                                                IconButton(
                                                    onClick = { playerInfo.playerId?.let { viewModel.kickPlayer(it) } },
                                                    modifier = Modifier.size(24.dp)
                                                ) {
                                                    Icon(Icons.Default.Close, "Kick", tint = Color(0xFFef4444), modifier = Modifier.size(16.dp))
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(24.dp))

                            // Player controls
                            val myPlayer = room?.players?.find { it.playerId == uiState.playerId }
                            val amReady = myPlayer?.isReady == 1

                            if (!uiState.isHost) {
                                MysticButton(
                                    text = if (amReady) "Batal Ready" else "Ready",
                                    onClick = { viewModel.readyPlayer(!amReady) }
                                )
                            }

                            // Host controls
                            if (uiState.isHost) {
                                MysticButton(
                                    text = "Mulai Game",
                                    onClick = { viewModel.startRoom() },
                                    enabled = !uiState.roomLoading
                                )
                            }

                            Spacer(modifier = Modifier.height(12.dp))
                            MysticButton(text = "Keluar Room", onClick = { viewModel.exitRoom() })

                            if (uiState.roomLoading) {
                                Spacer(modifier = Modifier.height(12.dp))
                                CircularProgressIndicator(color = TriviaCyan, modifier = Modifier.size(24.dp))
                            }

                            uiState.error?.let {
                                Spacer(modifier = Modifier.height(12.dp))
                                Card(shape = RoundedCornerShape(8.dp), colors = CardDefaults.cardColors(containerColor = Color(0xFFef4444).copy(alpha = 0.1f))) {
                                    Text(text = it, color = Color(0xFFef4444), style = MaterialTheme.typography.bodySmall, modifier = Modifier.padding(12.dp))
                                }
                            }
                        }
                    }

                    // Room Playing Phase
                    uiState.roomPhase == TriviaRoomPhase.ROOM_PLAYING && !uiState.roomComplete -> {
                        val question = uiState.roomQuestion
                        if (question != null) {
                            var countdownProgress by remember { mutableFloatStateOf(1f) }

                            LaunchedEffect(question) {
                                roomSelectedAnswer = null
                                countdownProgress = 1f
                                val totalDuration = 15_000L
                                val stepDelay = 50L
                                val steps = totalDuration / stepDelay
                                val decrement = 1f / steps
                                repeat(steps.toInt()) {
                                    delay(stepDelay)
                                    countdownProgress = (countdownProgress - decrement).coerceAtLeast(0f)
                                }
                                // Auto-submit when timer reaches zero
                                if (!uiState.roomShowFeedback && !uiState.roomLoading) {
                                    viewModel.answerRoomQuestion(question.id, "tidak menjawab")
                                }
                            }

                            Column(modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(16.dp)) {
                                Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                                    Text("Soal ${uiState.roomCurrentQuestion}/${uiState.roomTotalQuestions}", color = TextSecondary)
                                    Surface(shape = RoundedCornerShape(20.dp), color = TriviaCyan.copy(alpha = 0.15f)) {
                                        Text("Skor: ${uiState.roomScore}", color = TriviaCyan, fontWeight = FontWeight.Bold, modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp))
                                    }
                                }
                                Spacer(modifier = Modifier.height(8.dp))
                                LinearProgressIndicator(
                                    progress = { uiState.roomCurrentQuestion.toFloat() / uiState.roomTotalQuestions.coerceAtLeast(1) },
                                    modifier = Modifier.fillMaxWidth().height(6.dp).clip(RoundedCornerShape(3.dp)),
                                    color = TriviaCyan, trackColor = MysticSurface
                                )
                                Spacer(modifier = Modifier.height(16.dp))

                                Card(
                                    modifier = Modifier.fillMaxWidth().border(1.dp, TriviaCyan.copy(alpha = 0.5f), RoundedCornerShape(16.dp)),
                                    shape = RoundedCornerShape(16.dp),
                                    colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                                ) {
                                    Text(
                                        text = question.question ?: "",
                                        color = Color.White, style = MaterialTheme.typography.titleMedium,
                                        modifier = Modifier.padding(20.dp)
                                    )
                                }
                                Spacer(modifier = Modifier.height(12.dp))

                                // Countdown bar
                                Box(
                                    modifier = Modifier.fillMaxWidth().height(6.dp).clip(RoundedCornerShape(3.dp)).background(MysticSurface.copy(alpha = 0.5f))
                                ) {
                                    Box(
                                        modifier = Modifier.fillMaxHeight().fillMaxWidth(countdownProgress).clip(RoundedCornerShape(3.dp))
                                            .background(Brush.horizontalGradient(listOf(Color(0xFF6BB7FF), Color(0xFF7D55FF), Color(0xFFBD59FF))))
                                    )
                                }

                                Spacer(modifier = Modifier.height(20.dp))

                                // Answers
                                question.answers?.forEach { answer ->
                                    val isSelected = roomSelectedAnswer == answer
                                    val isCorrectAnswer = uiState.roomShowFeedback && answer == uiState.roomCorrectAnswer
                                    val isWrongSelected = uiState.roomShowFeedback && isSelected && uiState.roomLastAnswerCorrect == false

                                    val borderColor = when {
                                        isCorrectAnswer -> Color(0xFF22c55e)
                                        isWrongSelected -> Color(0xFFef4444)
                                        isSelected -> TriviaCyan
                                        else -> Color.White.copy(alpha = 0.1f)
                                    }
                                    val bgColor = when {
                                        isCorrectAnswer -> Color(0xFF22c55e).copy(alpha = 0.15f)
                                        isWrongSelected -> Color(0xFFef4444).copy(alpha = 0.15f)
                                        isSelected -> TriviaCyan.copy(alpha = 0.15f)
                                        else -> Color.White.copy(alpha = 0.05f)
                                    }

                                    Card(
                                        modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp).border(1.dp, borderColor, RoundedCornerShape(12.dp)),
                                        shape = RoundedCornerShape(12.dp),
                                        colors = CardDefaults.cardColors(containerColor = bgColor),
                                        onClick = {
                                            if (!uiState.roomLoading && !uiState.roomShowFeedback) {
                                                roomSelectedAnswer = answer
                                            }
                                        }
                                    ) {
                                        Row(modifier = Modifier.padding(16.dp), verticalAlignment = Alignment.CenterVertically) {
                                            Text(
                                                text = answer, modifier = Modifier.weight(1f),
                                                color = when {
                                                    isCorrectAnswer -> Color(0xFF22c55e)
                                                    isWrongSelected -> Color(0xFFef4444)
                                                    isSelected -> TriviaCyan
                                                    else -> TextSecondary
                                                },
                                                style = MaterialTheme.typography.bodyLarge
                                            )
                                            if (isCorrectAnswer) {
                                                Icon(Icons.Default.CheckCircle, null, tint = Color(0xFF22c55e), modifier = Modifier.size(20.dp))
                                            } else if (isWrongSelected) {
                                                Icon(Icons.Default.Close, null, tint = Color(0xFFef4444), modifier = Modifier.size(20.dp))
                                            }
                                        }
                                    }
                                }

                                Spacer(modifier = Modifier.height(20.dp))

                                if (roomSelectedAnswer != null && !uiState.roomShowFeedback) {
                                    MysticButton(
                                        text = "Jawab",
                                        onClick = {
                                            roomSelectedAnswer?.let { answer ->
                                                viewModel.answerRoomQuestion(question.id, answer)
                                            }
                                        },
                                        enabled = !uiState.roomLoading
                                    )
                                }

                                if (uiState.roomShowFeedback) {
                                    Spacer(modifier = Modifier.height(12.dp))
                                    Surface(
                                        shape = RoundedCornerShape(12.dp),
                                        color = if (uiState.roomLastAnswerCorrect == true) Color(0xFF22c55e).copy(alpha = 0.15f) else Color(0xFFef4444).copy(alpha = 0.15f)
                                    ) {
                                        Text(
                                            text = if (uiState.roomLastAnswerCorrect == true) "\u2705 Benar!" else "\u274C Salah! Jawaban: ${uiState.roomCorrectAnswer}",
                                            color = if (uiState.roomLastAnswerCorrect == true) Color(0xFF22c55e) else Color(0xFFef4444),
                                            fontWeight = FontWeight.Medium,
                                            modifier = Modifier.padding(12.dp)
                                        )
                                    }
                                }

                                if (uiState.roomLoading) {
                                    Spacer(modifier = Modifier.height(16.dp))
                                    Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                                        CircularProgressIndicator(color = TriviaCyan, modifier = Modifier.size(32.dp))
                                    }
                                }
                            }
                        } else {
                            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                    CircularProgressIndicator(color = TriviaCyan)
                                    Spacer(modifier = Modifier.height(12.dp))
                                    Text("Menunggu soal...", color = TextSecondary)
                                }
                            }
                        }
                    }

                    // Room Playing - Complete, auto-finish
                    uiState.roomPhase == TriviaRoomPhase.ROOM_PLAYING && uiState.roomComplete -> {
                        LaunchedEffect(Unit) { viewModel.finishRoom() }
                        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                CircularProgressIndicator(color = TriviaCyan)
                                Spacer(modifier = Modifier.height(12.dp))
                                Text("Menghitung skor...", color = TextSecondary)
                            }
                        }
                    }

                    // Room Result Phase
                    uiState.roomPhase == TriviaRoomPhase.ROOM_RESULT -> {
                        Column(
                            modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Spacer(modifier = Modifier.height(16.dp))
                            Box(
                                modifier = Modifier.size(80.dp).clip(CircleShape)
                                    .background(Brush.radialGradient(listOf(TriviaCyan.copy(alpha = 0.3f), Color.Transparent))),
                                contentAlignment = Alignment.Center
                            ) { Text("\uD83C\uDFC6", fontSize = 48.sp) }

                            Spacer(modifier = Modifier.height(16.dp))
                            Text(
                                text = if (uiState.roomFinished) "Hasil Akhir" else "Menunggu pemain lain...",
                                color = TriviaCyan, fontSize = 22.sp, fontWeight = FontWeight.Bold
                            )

                            if (!uiState.roomFinished) {
                                Spacer(modifier = Modifier.height(8.dp))
                                CircularProgressIndicator(color = TriviaCyan, modifier = Modifier.size(24.dp))
                            }

                            Spacer(modifier = Modifier.height(16.dp))

                            // Leaderboard
                            uiState.roomLeaderboard.forEachIndexed { index, entry ->
                                val isWinner = index == 0 && uiState.roomFinished
                                Card(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .padding(vertical = 4.dp)
                                        .border(
                                            width = if (isWinner) 2.dp else 1.dp,
                                            color = if (isWinner) Color(0xFFFFD700) else TriviaCyan.copy(alpha = 0.2f),
                                            shape = RoundedCornerShape(12.dp)
                                        ),
                                    shape = RoundedCornerShape(12.dp),
                                    colors = CardDefaults.cardColors(
                                        containerColor = if (isWinner) Color(0xFFFFD700).copy(alpha = 0.1f) else MysticDarkOverlay
                                    )
                                ) {
                                    Row(
                                        modifier = Modifier.padding(12.dp).fillMaxWidth(),
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Box(
                                            modifier = Modifier.size(32.dp).clip(CircleShape)
                                                .background(if (isWinner) Color(0xFFFFD700).copy(alpha = 0.3f) else TriviaCyan.copy(alpha = 0.2f)),
                                            contentAlignment = Alignment.Center
                                        ) {
                                            Text("${index + 1}", color = if (isWinner) Color(0xFFFFD700) else TriviaCyan, fontWeight = FontWeight.Bold)
                                        }
                                        Spacer(modifier = Modifier.width(12.dp))
                                        Column(modifier = Modifier.weight(1f)) {
                                            Text(
                                                text = entry.name ?: "Player #${entry.playerId}",
                                                color = Color.White,
                                                fontWeight = FontWeight.Medium
                                            )
                                            Text(
                                                text = "Durasi: ${entry.duration ?: 0}s",
                                                color = TextSecondary,
                                                style = MaterialTheme.typography.bodySmall
                                            )
                                        }
                                        Text(
                                            text = "${entry.score ?: 0} pts",
                                            color = TriviaCyan,
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 16.sp
                                        )
                                    }
                                }
                            }

                            if (uiState.roomLeaderboard.isEmpty()) {
                                Text("Menunggu data leaderboard...", color = TextSecondary)
                            }

                            Spacer(modifier = Modifier.height(24.dp))
                            MysticButton(text = "Keluar Room", onClick = { viewModel.exitRoom() })
                        }
                    }

                    // Statistics overlay
                    uiState.showStats -> {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .verticalScroll(rememberScrollState())
                                .padding(16.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text("Statistik", color = TriviaCyan, fontSize = 24.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(16.dp))

                            if (uiState.statsLoading) {
                                CircularProgressIndicator(color = TriviaCyan)
                            } else {
                                uiState.statisticsData?.let { stats ->
                                    Card(
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .border(1.dp, TriviaCyan.copy(alpha = 0.3f), RoundedCornerShape(16.dp)),
                                        shape = RoundedCornerShape(16.dp),
                                        colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                                    ) {
                                        Column(modifier = Modifier.padding(20.dp)) {
                                            TriviaStatRow("Total Dimainkan", "${stats.totalPlayed ?: 0}", TriviaCyan)
                                            Spacer(modifier = Modifier.height(12.dp))
                                            TriviaStatRow("Total Benar", "${stats.totalCorrect ?: 0}", TriviaCyan)
                                            Spacer(modifier = Modifier.height(12.dp))
                                            TriviaStatRow("Total Salah", "${stats.totalWrong ?: 0}", TriviaCyan)
                                            Spacer(modifier = Modifier.height(12.dp))
                                            TriviaStatRow("Skor Tertinggi", "${stats.highestScore ?: 0}", TriviaCyan)
                                            Spacer(modifier = Modifier.height(12.dp))
                                            TriviaStatRow("Akurasi Rata-rata", "${String.format("%.1f", stats.averageAccuracy ?: 0.0)}%", TriviaCyan)
                                        }
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(24.dp))
                            MysticButton(text = "Tutup", onClick = { viewModel.toggleStats() })
                        }
                    }

                    // Leaderboard overlay
                    uiState.showLeaderboard -> {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .verticalScroll(rememberScrollState())
                                .padding(16.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text("Leaderboard", color = TriviaCyan, fontSize = 24.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(16.dp))

                            if (uiState.leaderboardLoading) {
                                CircularProgressIndicator(color = TriviaCyan)
                            } else {
                                uiState.leaderboard.take(10).forEachIndexed { index, entry ->
                                    Card(
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .padding(vertical = 4.dp)
                                            .border(1.dp, TriviaCyan.copy(alpha = 0.2f), RoundedCornerShape(12.dp)),
                                        shape = RoundedCornerShape(12.dp),
                                        colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                                    ) {
                                        Row(
                                            modifier = Modifier.padding(12.dp),
                                            verticalAlignment = Alignment.CenterVertically
                                        ) {
                                            // Rank
                                            Box(
                                                modifier = Modifier
                                                    .size(32.dp)
                                                    .clip(CircleShape)
                                                    .background(TriviaCyan.copy(alpha = 0.2f)),
                                                contentAlignment = Alignment.Center
                                            ) {
                                                Text(
                                                    text = "${index + 1}",
                                                    color = TriviaCyan,
                                                    fontWeight = FontWeight.Bold,
                                                    fontSize = 14.sp
                                                )
                                            }
                                            Spacer(modifier = Modifier.width(12.dp))
                                            Column(modifier = Modifier.weight(1f)) {
                                                Text(
                                                    text = entry.name ?: "Unknown",
                                                    color = Color.White,
                                                    fontWeight = FontWeight.Medium
                                                )
                                                Text(
                                                    text = "Skor: ${entry.highestScore ?: 0} | Streak: ${entry.highestStreak ?: 0}",
                                                    color = TextSecondary,
                                                    style = MaterialTheme.typography.bodySmall
                                                )
                                            }
                                        }
                                    }
                                }

                                if (uiState.leaderboard.isEmpty()) {
                                    Text("Belum ada data leaderboard", color = TextSecondary)
                                }
                            }

                            Spacer(modifier = Modifier.height(24.dp))
                            MysticButton(text = "Tutup", onClick = { viewModel.toggleLeaderboard() })
                        }
                    }

                    // Finished
                    uiState.isComplete && uiState.finishResponse != null -> {
                        val finish = uiState.finishResponse!!
                        Column(
                            modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Spacer(modifier = Modifier.height(16.dp))
                            Box(
                                modifier = Modifier.size(100.dp).clip(CircleShape)
                                    .background(Brush.radialGradient(listOf(TriviaCyan.copy(alpha = 0.3f), Color.Transparent))),
                                contentAlignment = Alignment.Center
                            ) { Text("\uD83C\uDFC6", fontSize = 56.sp) }

                            Spacer(modifier = Modifier.height(20.dp))
                            Text("Permainan Selesai!", color = TriviaCyan, fontSize = 26.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(24.dp))

                            Card(
                                modifier = Modifier.fillMaxWidth().border(1.dp, TriviaCyan.copy(alpha = 0.3f), RoundedCornerShape(20.dp)),
                                shape = RoundedCornerShape(20.dp),
                                colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                            ) {
                                Column(modifier = Modifier.padding(32.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                                    Text("Skor", color = TextSecondary)
                                    Spacer(modifier = Modifier.height(8.dp))
                                    Text("${finish.score ?: 0}", color = TriviaCyan, fontSize = 64.sp, fontWeight = FontWeight.Bold)
                                    Spacer(modifier = Modifier.height(16.dp))
                                    HorizontalDivider(color = TriviaCyan.copy(alpha = 0.2f))
                                    Spacer(modifier = Modifier.height(12.dp))
                                    Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceEvenly) {
                                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                            Text("${finish.streak ?: 0} \uD83D\uDD25", color = TriviaCyan, fontWeight = FontWeight.Bold, fontSize = 20.sp)
                                            Text("Streak", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                        }
                                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                            Text("${finish.durationSeconds ?: 0}s", color = TriviaCyan, fontWeight = FontWeight.Bold, fontSize = 20.sp)
                                            Text("Durasi", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                        }
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(32.dp))
                            MysticButton(text = "Main Lagi", onClick = { selectedAnswer = null; viewModel.startGame(category.ifBlank { null }, questionCount.toInt()) })
                        }
                    }

                    // Auto-finish
                    uiState.isComplete && uiState.finishResponse == null -> {
                        LaunchedEffect(Unit) { viewModel.finishGame() }
                        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                CircularProgressIndicator(color = TriviaCyan)
                                Spacer(modifier = Modifier.height(12.dp))
                                Text("Menghitung skor...", color = TextSecondary)
                            }
                        }
                    }

                    // Playing
                    uiState.currentQuestion != null -> {
                        // Visual countdown timer state
                        var countdownProgress by remember { mutableFloatStateOf(1f) }

                        LaunchedEffect(uiState.currentQuestion) {
                            countdownProgress = 1f
                            val totalDuration = 10_000L
                            val stepDelay = 50L
                            val steps = totalDuration / stepDelay
                            val decrement = 1f / steps
                            repeat(steps.toInt()) {
                                delay(stepDelay)
                                countdownProgress = (countdownProgress - decrement).coerceAtLeast(0f)
                            }
                        }

                        Column(modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(16.dp)) {
                            // Header
                            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                                Text("Soal ${uiState.currentQuestionNumber}/${uiState.totalQuestions}", color = TextSecondary)
                                Surface(shape = RoundedCornerShape(20.dp), color = TriviaCyan.copy(alpha = 0.15f)) {
                                    Text("\uD83D\uDD25 ${uiState.streak}", color = TriviaCyan, fontWeight = FontWeight.Bold, modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp))
                                }
                            }
                            Spacer(modifier = Modifier.height(8.dp))
                            LinearProgressIndicator(
                                progress = { uiState.currentQuestionNumber.toFloat() / uiState.totalQuestions.coerceAtLeast(1) },
                                modifier = Modifier.fillMaxWidth().height(6.dp).clip(RoundedCornerShape(3.dp)),
                                color = TriviaCyan, trackColor = MysticSurface
                            )
                            Spacer(modifier = Modifier.height(16.dp))

                            // Question card with cyan border and dark overlay bg
                            Card(
                                modifier = Modifier.fillMaxWidth().border(1.dp, TriviaCyan.copy(alpha = 0.5f), RoundedCornerShape(16.dp)),
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                            ) {
                                Text(
                                    text = uiState.currentQuestion?.question ?: "",
                                    color = Color.White, style = MaterialTheme.typography.titleMedium,
                                    modifier = Modifier.padding(20.dp)
                                )
                            }
                            Spacer(modifier = Modifier.height(12.dp))

                            // Decorative countdown bar with gradient
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(6.dp)
                                    .clip(RoundedCornerShape(3.dp))
                                    .background(MysticSurface.copy(alpha = 0.5f))
                            ) {
                                Box(
                                    modifier = Modifier
                                        .fillMaxHeight()
                                        .fillMaxWidth(countdownProgress)
                                        .clip(RoundedCornerShape(3.dp))
                                        .background(
                                            Brush.horizontalGradient(
                                                colors = listOf(
                                                    Color(0xFF6BB7FF),
                                                    Color(0xFF7D55FF),
                                                    Color(0xFFBD59FF)
                                                )
                                            )
                                        )
                                )
                            }

                            Spacer(modifier = Modifier.height(20.dp))

                            // Answers
                            uiState.currentQuestion?.answers?.forEach { answer ->
                                val isSelected = selectedAnswer == answer
                                val isCorrectAnswer = uiState.showFeedback && answer == uiState.correctAnswer
                                val isWrongSelected = uiState.showFeedback && isSelected && uiState.lastAnswerCorrect == false

                                val borderColor = when {
                                    isCorrectAnswer -> Color(0xFF22c55e)
                                    isWrongSelected -> Color(0xFFef4444)
                                    isSelected -> TriviaCyan
                                    else -> Color.White.copy(alpha = 0.1f)
                                }
                                val bgColor = when {
                                    isCorrectAnswer -> Color(0xFF22c55e).copy(alpha = 0.15f)
                                    isWrongSelected -> Color(0xFFef4444).copy(alpha = 0.15f)
                                    isSelected -> TriviaCyan.copy(alpha = 0.15f)
                                    else -> Color.White.copy(alpha = 0.05f)
                                }

                                Card(
                                    modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)
                                        .border(1.dp, borderColor, RoundedCornerShape(12.dp)),
                                    shape = RoundedCornerShape(12.dp),
                                    colors = CardDefaults.cardColors(containerColor = bgColor),
                                    onClick = {
                                        if (!uiState.isLoading && !uiState.showFeedback) {
                                            selectedAnswer = answer
                                            SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                        }
                                    }
                                ) {
                                    Row(modifier = Modifier.padding(16.dp), verticalAlignment = Alignment.CenterVertically) {
                                        Text(
                                            text = answer, modifier = Modifier.weight(1f),
                                            color = when {
                                                isCorrectAnswer -> Color(0xFF22c55e)
                                                isWrongSelected -> Color(0xFFef4444)
                                                isSelected -> TriviaCyan
                                                else -> TextSecondary
                                            },
                                            style = MaterialTheme.typography.bodyLarge
                                        )
                                        if (isCorrectAnswer) {
                                            Icon(Icons.Default.CheckCircle, null, tint = Color(0xFF22c55e), modifier = Modifier.size(20.dp))
                                        } else if (isWrongSelected) {
                                            Icon(Icons.Default.Close, null, tint = Color(0xFFef4444), modifier = Modifier.size(20.dp))
                                        }
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(20.dp))

                            if (selectedAnswer != null && !uiState.showFeedback) {
                                MysticButton(
                                    text = "Jawab",
                                    onClick = {
                                        uiState.currentQuestion?.let { q ->
                                            selectedAnswer?.let { answer ->
                                                viewModel.answerQuestion(q.id, answer)
                                            }
                                        }
                                    },
                                    enabled = !uiState.isLoading
                                )
                            }

                            // Feedback message
                            if (uiState.showFeedback) {
                                Spacer(modifier = Modifier.height(12.dp))
                                Surface(
                                    shape = RoundedCornerShape(12.dp),
                                    color = if (uiState.lastAnswerCorrect == true) Color(0xFF22c55e).copy(alpha = 0.15f) else Color(0xFFef4444).copy(alpha = 0.15f)
                                ) {
                                    Row(modifier = Modifier.padding(12.dp), verticalAlignment = Alignment.CenterVertically) {
                                        Text(
                                            text = if (uiState.lastAnswerCorrect == true) "\u2705 Benar!" else "\u274C Salah! Jawaban: ${uiState.correctAnswer}",
                                            color = if (uiState.lastAnswerCorrect == true) Color(0xFF22c55e) else Color(0xFFef4444),
                                            fontWeight = FontWeight.Medium
                                        )
                                    }
                                }
                                // Reset selectedAnswer after feedback shown
                                LaunchedEffect(uiState.showFeedback) {
                                    if (!uiState.showFeedback) selectedAnswer = null
                                }
                            }

                            if (uiState.isLoading) {
                                Spacer(modifier = Modifier.height(16.dp))
                                Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                                    CircularProgressIndicator(color = TriviaCyan, modifier = Modifier.size(32.dp))
                                }
                            }

                            uiState.error?.let {
                                Spacer(modifier = Modifier.height(12.dp))
                                Card(shape = RoundedCornerShape(8.dp), colors = CardDefaults.cardColors(containerColor = Color(0xFFef4444).copy(alpha = 0.1f))) {
                                    Text(text = it, color = Color(0xFFef4444), style = MaterialTheme.typography.bodySmall, modifier = Modifier.padding(12.dp))
                                }
                            }
                        }
                    }

                    // Pre-game
                    uiState.sessionId == null && !uiState.isLoading -> {
                        Column(
                            modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center
                        ) {
                            Box(
                                modifier = Modifier.size(120.dp).clip(CircleShape)
                                    .background(Brush.radialGradient(listOf(TriviaCyan.copy(alpha = 0.4f), TriviaCyan.copy(alpha = 0.1f), Color.Transparent))),
                                contentAlignment = Alignment.Center
                            ) { Text("\uD83E\uDDE0", fontSize = 64.sp) }

                            Spacer(modifier = Modifier.height(24.dp))
                            Text("Trivia Game", color = TriviaCyan, fontSize = 28.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(12.dp))
                            Text(
                                text = "Uji pengetahuanmu! Pilih kategori dan jumlah soal, lalu jawab pertanyaan-pertanyaan yang menantang.",
                                color = TextSecondary, textAlign = TextAlign.Center, style = MaterialTheme.typography.bodyMedium
                            )
                            Spacer(modifier = Modifier.height(24.dp))

                            MysticTextField(value = category, onValueChange = { category = it }, label = "Kategori (opsional, contoh: sains)")
                            Spacer(modifier = Modifier.height(16.dp))

                            Text("Jumlah Soal: ${questionCount.toInt()}", color = TextSecondary)
                            Slider(
                                value = questionCount, onValueChange = { questionCount = it },
                                valueRange = 5f..30f, steps = 4,
                                colors = SliderDefaults.colors(thumbColor = TriviaCyan, activeTrackColor = TriviaCyan)
                            )

                            Spacer(modifier = Modifier.height(8.dp))
                            Card(
                                modifier = Modifier.fillMaxWidth().border(1.dp, TriviaCyan.copy(alpha = 0.3f), RoundedCornerShape(16.dp)),
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                            ) {
                                Column(modifier = Modifier.padding(16.dp)) {
                                    Text("Cara Bermain:", color = TriviaCyan, fontWeight = FontWeight.SemiBold)
                                    Spacer(modifier = Modifier.height(8.dp))
                                    Text("\u2022 Pilih kategori atau kosongkan untuk random", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                    Text("\u2022 Jawab pertanyaan dengan benar", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                    Text("\u2022 Bangun streak untuk skor lebih tinggi!", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                    Text("\u2022 Jawaban benar = +10 poin", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                }
                            }

                            Spacer(modifier = Modifier.height(24.dp))
                            MysticButton(text = "Mulai Permainan", onClick = { viewModel.startGame(category.ifBlank { null }, questionCount.toInt()) })
                            Spacer(modifier = Modifier.height(12.dp))
                            MysticButton(text = "Main Bersama", onClick = { viewModel.enterRoomMode() })

                            uiState.error?.let {
                                Spacer(modifier = Modifier.height(12.dp))
                                Card(shape = RoundedCornerShape(8.dp), colors = CardDefaults.cardColors(containerColor = Color(0xFFef4444).copy(alpha = 0.1f))) {
                                    Text(text = it, color = Color(0xFFef4444), style = MaterialTheme.typography.bodySmall, modifier = Modifier.padding(12.dp))
                                }
                            }
                        }
                    }

                    // Loading
                    else -> {
                        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                CircularProgressIndicator(color = TriviaCyan)
                                Spacer(modifier = Modifier.height(12.dp))
                                Text("Menyiapkan pertanyaan...", color = TextSecondary)
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun TriviaStatRow(label: String, value: String, color: Color) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(text = label, color = TextSecondary, style = MaterialTheme.typography.bodyMedium)
        Text(text = value, color = color, fontWeight = FontWeight.Bold, fontSize = 18.sp)
    }
}
