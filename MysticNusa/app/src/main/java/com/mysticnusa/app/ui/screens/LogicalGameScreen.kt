package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.CheckCircle
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
import com.mysticnusa.app.ui.components.SoundManager
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.LogicalViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LogicalGameScreen(navController: NavController) {
    val viewModel: LogicalViewModel = viewModel(
        factory = LogicalViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()
    var selectedAnswerId by remember { mutableStateOf<String?>(null) }
    val context = LocalContext.current

    // BGM lifecycle
    LaunchedEffect(Unit) {
        SoundManager.playBgm(context, "https://mystical-nusa.web.id/sound/bgm/bgm-iq-normal.mp3")
    }
    DisposableEffect(Unit) {
        onDispose {
            SoundManager.stopBgm()
        }
    }

    Box(modifier = Modifier.fillMaxSize()) {
        // Background layer
        GameBackground(
            imageUrl = "https://mystical-nusa.web.id/images/asset/games/background/logical-background-large.webp"
        )

        // Content layer
        Scaffold(
            topBar = {
                TopAppBar(
                    title = { Text("Tes Logika / IQ", color = MysticGold) },
                    navigationIcon = {
                        IconButton(onClick = { navController.popBackStack() }) {
                            Icon(Icons.AutoMirrored.Filled.ArrowBack, "Back", tint = MysticGold)
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
                    // Finished with results
                    uiState.isComplete && uiState.finishResponse != null -> {
                        val finish = uiState.finishResponse!!
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .verticalScroll(rememberScrollState())
                                .padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Spacer(modifier = Modifier.height(16.dp))
                            Box(
                                modifier = Modifier
                                    .size(100.dp)
                                    .clip(CircleShape)
                                    .background(Brush.radialGradient(listOf(MysticGold.copy(alpha = 0.3f), Color.Transparent))),
                                contentAlignment = Alignment.Center
                            ) { Text("\uD83E\uDDE0", fontSize = 56.sp) }

                            Spacer(modifier = Modifier.height(20.dp))
                            Text("Tes Selesai!", color = MysticGold, fontSize = 26.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(24.dp))

                            // IQ Score card
                            Card(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .border(1.dp, MysticGold.copy(alpha = 0.3f), RoundedCornerShape(20.dp)),
                                shape = RoundedCornerShape(20.dp),
                                colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                            ) {
                                Column(modifier = Modifier.padding(32.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                                    Text("IQ Score", color = TextSecondary, style = MaterialTheme.typography.bodyMedium)
                                    Spacer(modifier = Modifier.height(8.dp))
                                    Text(
                                        text = "${finish.iq ?: 100}",
                                        color = MysticGold,
                                        fontSize = 64.sp,
                                        fontWeight = FontWeight.Bold
                                    )
                                    Spacer(modifier = Modifier.height(8.dp))
                                    Surface(shape = RoundedCornerShape(20.dp), color = MysticPurple.copy(alpha = 0.2f)) {
                                        Text(
                                            text = finish.category ?: "Normal",
                                            color = MysticPurpleLight,
                                            fontWeight = FontWeight.Medium,
                                            modifier = Modifier.padding(horizontal = 16.dp, vertical = 6.dp)
                                        )
                                    }
                                    Spacer(modifier = Modifier.height(12.dp))
                                    Text(
                                        text = finish.message ?: "",
                                        color = TextSecondary,
                                        textAlign = TextAlign.Center,
                                        style = MaterialTheme.typography.bodyMedium
                                    )
                                    Spacer(modifier = Modifier.height(16.dp))
                                    HorizontalDivider(color = MysticGold.copy(alpha = 0.2f))
                                    Spacer(modifier = Modifier.height(12.dp))
                                    Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceEvenly) {
                                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                            Text("${finish.totalPoint ?: 0}", color = MysticGold, fontWeight = FontWeight.Bold, fontSize = 20.sp)
                                            Text("Poin", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                        }
                                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                            Text("${finish.durationSeconds ?: 0}s", color = MysticGold, fontWeight = FontWeight.Bold, fontSize = 20.sp)
                                            Text("Durasi", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                        }
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(32.dp))
                            MysticButton(text = "Main Lagi", onClick = { selectedAnswerId = null; viewModel.startGame() })
                        }
                    }

                    // Auto-finish (all questions answered)
                    uiState.isComplete && uiState.finishResponse == null -> {
                        LaunchedEffect(Unit) { viewModel.finishGame() }
                        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                CircularProgressIndicator(color = MysticGold)
                                Spacer(modifier = Modifier.height(12.dp))
                                Text("Menghitung skor IQ...", color = TextSecondary)
                            }
                        }
                    }

                    // Playing
                    uiState.currentQuestion != null -> {
                        Column(
                            modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(16.dp)
                        ) {
                            // Progress header
                            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                                Text("Soal ${uiState.currentQuestionNumber}/${uiState.totalQuestions}", color = TextSecondary)
                                Surface(shape = RoundedCornerShape(20.dp), color = MysticGold.copy(alpha = 0.15f)) {
                                    Text(
                                        text = "\uD83E\uDDE0 IQ Test",
                                        color = MysticGold,
                                        modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp),
                                        style = MaterialTheme.typography.labelSmall
                                    )
                                }
                            }
                            Spacer(modifier = Modifier.height(8.dp))
                            LinearProgressIndicator(
                                progress = { uiState.currentQuestionNumber.toFloat() / uiState.totalQuestions.coerceAtLeast(1) },
                                modifier = Modifier.fillMaxWidth().height(6.dp).clip(RoundedCornerShape(3.dp)),
                                color = MysticGold,
                                trackColor = MysticSurface
                            )
                            Spacer(modifier = Modifier.height(24.dp))

                            // Question card with semi-transparent dark background and gold border
                            Card(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .border(1.dp, MysticGold.copy(alpha = 0.3f), RoundedCornerShape(16.dp)),
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                            ) {
                                Text(
                                    text = uiState.currentQuestion?.question ?: "",
                                    color = Color.White,
                                    style = MaterialTheme.typography.titleMedium,
                                    modifier = Modifier.padding(20.dp)
                                )
                            }

                            Spacer(modifier = Modifier.height(20.dp))

                            // Answer options with semi-transparent background and gold border on selection
                            uiState.currentQuestion?.answers?.forEach { answer ->
                                val isSelected = selectedAnswerId == answer.id
                                Card(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .padding(vertical = 4.dp)
                                        .border(
                                            width = if (isSelected) 2.dp else 1.dp,
                                            color = if (isSelected) MysticGold else MysticGold.copy(alpha = 0.15f),
                                            shape = RoundedCornerShape(12.dp)
                                        ),
                                    shape = RoundedCornerShape(12.dp),
                                    colors = CardDefaults.cardColors(
                                        containerColor = if (isSelected) MysticGold.copy(alpha = 0.15f) else Color.White.copy(alpha = 0.05f)
                                    ),
                                    onClick = {
                                        if (!uiState.isLoading) {
                                            selectedAnswerId = answer.id
                                            SoundManager.playSound(context, "https://mystical-nusa.web.id/sound/sfx/click2.wav")
                                        }
                                    }
                                ) {
                                    Row(modifier = Modifier.padding(16.dp), verticalAlignment = Alignment.CenterVertically) {
                                        Text(
                                            text = answer.text ?: "",
                                            color = if (isSelected) MysticGold else TextSecondary,
                                            style = MaterialTheme.typography.bodyLarge,
                                            modifier = Modifier.weight(1f)
                                        )
                                        if (isSelected) {
                                            Icon(Icons.Default.CheckCircle, "Selected", tint = MysticGold, modifier = Modifier.size(20.dp))
                                        }
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(20.dp))

                            if (selectedAnswerId != null) {
                                MysticButton(
                                    text = "Jawab",
                                    onClick = {
                                        uiState.currentQuestion?.let { q ->
                                            selectedAnswerId?.let { aid ->
                                                viewModel.answerQuestion(q.id, aid)
                                                selectedAnswerId = null
                                            }
                                        }
                                    },
                                    enabled = !uiState.isLoading
                                )
                            }

                            if (uiState.isLoading) {
                                Spacer(modifier = Modifier.height(16.dp))
                                Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                                    CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
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
                    uiState.matchId == null && !uiState.isLoading -> {
                        Column(
                            modifier = Modifier.fillMaxSize().padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center
                        ) {
                            Box(
                                modifier = Modifier.size(120.dp).clip(CircleShape)
                                    .background(Brush.radialGradient(listOf(MysticGold.copy(alpha = 0.4f), MysticGold.copy(alpha = 0.1f), Color.Transparent))),
                                contentAlignment = Alignment.Center
                            ) { Text("\uD83E\uDDE0", fontSize = 64.sp) }

                            Spacer(modifier = Modifier.height(24.dp))
                            Text("Tes Logika / IQ", color = MysticGold, fontSize = 28.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(12.dp))
                            Text(
                                text = "Uji kemampuan logikamu! Jawab 10 pertanyaan untuk mengukur skor IQ-mu.",
                                color = TextSecondary, textAlign = TextAlign.Center, style = MaterialTheme.typography.bodyMedium
                            )
                            Spacer(modifier = Modifier.height(16.dp))

                            Card(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .border(1.dp, MysticGold.copy(alpha = 0.3f), RoundedCornerShape(16.dp)),
                                shape = RoundedCornerShape(16.dp),
                                colors = CardDefaults.cardColors(containerColor = MysticDarkOverlay)
                            ) {
                                Column(modifier = Modifier.padding(16.dp)) {
                                    Text("Tentang Tes:", color = MysticGold, fontWeight = FontWeight.SemiBold)
                                    Spacer(modifier = Modifier.height(8.dp))
                                    Text("\u2022 10 soal logika dengan tingkat kesulitan bervariasi", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                    Text("\u2022 Skor dihitung berdasarkan jawaban benar", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                    Text("\u2022 Bonus poin jika selesai cepat (skor sempurna)", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                    Text("\u2022 Hasil IQ berdasarkan skala standar", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                }
                            }

                            Spacer(modifier = Modifier.height(32.dp))
                            MysticButton(text = "Mulai Tes IQ", onClick = { viewModel.startGame() })

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
                                CircularProgressIndicator(color = MysticGold)
                                Spacer(modifier = Modifier.height(12.dp))
                                Text("Mempersiapkan soal...", color = TextSecondary)
                            }
                        }
                    }
                }
            }
        }
    }
}
