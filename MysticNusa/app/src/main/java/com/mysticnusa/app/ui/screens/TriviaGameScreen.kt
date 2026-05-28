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
import androidx.compose.material.icons.filled.Close
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.repository.GamesRepository
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticTextField
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.TriviaViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TriviaGameScreen(navController: NavController) {
    val viewModel: TriviaViewModel = viewModel(
        factory = TriviaViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()

    var category by remember { mutableStateOf("") }
    var questionCount by remember { mutableStateOf(10f) }
    var selectedAnswer by remember { mutableStateOf<String?>(null) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Trivia", color = MysticGold) },
                navigationIcon = {
                    IconButton(onClick = { navController.popBackStack() }) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, "Back", tint = MysticGold)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = MaterialTheme.colorScheme.background)
            )
        },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        Box(modifier = Modifier.padding(paddingValues).fillMaxSize()) {
            when {
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
                                .background(Brush.radialGradient(listOf(MysticGold.copy(alpha = 0.3f), Color.Transparent))),
                            contentAlignment = Alignment.Center
                        ) { Text("\uD83C\uDFC6", fontSize = 56.sp) }

                        Spacer(modifier = Modifier.height(20.dp))
                        Text("Permainan Selesai!", color = MysticGold, fontSize = 26.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(24.dp))

                        Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(20.dp), colors = CardDefaults.cardColors(containerColor = MysticSurface)) {
                            Column(modifier = Modifier.padding(32.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                                Text("Skor", color = TextSecondary)
                                Spacer(modifier = Modifier.height(8.dp))
                                Text("${finish.score ?: 0}", color = MysticGold, fontSize = 64.sp, fontWeight = FontWeight.Bold)
                                Spacer(modifier = Modifier.height(16.dp))
                                HorizontalDivider(color = MysticPurple.copy(alpha = 0.2f))
                                Spacer(modifier = Modifier.height(12.dp))
                                Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceEvenly) {
                                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                        Text("${finish.streak ?: 0} \uD83D\uDD25", color = MysticGold, fontWeight = FontWeight.Bold, fontSize = 20.sp)
                                        Text("Streak", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                    }
                                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                        Text("${finish.durationSeconds ?: 0}s", color = MysticGold, fontWeight = FontWeight.Bold, fontSize = 20.sp)
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
                            CircularProgressIndicator(color = MysticGold)
                            Spacer(modifier = Modifier.height(12.dp))
                            Text("Menghitung skor...", color = TextSecondary)
                        }
                    }
                }

                // Playing
                uiState.currentQuestion != null -> {
                    Column(modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(16.dp)) {
                        // Header
                        Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                            Text("Soal ${uiState.currentQuestionNumber}/${uiState.totalQuestions}", color = TextSecondary)
                            Surface(shape = RoundedCornerShape(20.dp), color = MysticGold.copy(alpha = 0.15f)) {
                                Text("\uD83D\uDD25 ${uiState.streak}", color = MysticGold, fontWeight = FontWeight.Bold, modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp))
                            }
                        }
                        Spacer(modifier = Modifier.height(8.dp))
                        LinearProgressIndicator(
                            progress = { uiState.currentQuestionNumber.toFloat() / uiState.totalQuestions.coerceAtLeast(1) },
                            modifier = Modifier.fillMaxWidth().height(6.dp).clip(RoundedCornerShape(3.dp)),
                            color = MysticGold, trackColor = MysticSurface
                        )
                        Spacer(modifier = Modifier.height(24.dp))

                        // Question
                        Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp), colors = CardDefaults.cardColors(containerColor = MysticSurface)) {
                            Text(
                                text = uiState.currentQuestion?.question ?: "",
                                color = Color.White, style = MaterialTheme.typography.titleMedium,
                                modifier = Modifier.padding(20.dp)
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
                                isSelected -> MysticGold
                                else -> Color.Transparent
                            }
                            val bgColor = when {
                                isCorrectAnswer -> Color(0xFF22c55e).copy(alpha = 0.15f)
                                isWrongSelected -> Color(0xFFef4444).copy(alpha = 0.15f)
                                isSelected -> MysticPurple.copy(alpha = 0.2f)
                                else -> MysticSurface
                            }

                            Card(
                                modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)
                                    .then(if (borderColor != Color.Transparent) Modifier.border(2.dp, borderColor, RoundedCornerShape(12.dp)) else Modifier),
                                shape = RoundedCornerShape(12.dp),
                                colors = CardDefaults.cardColors(containerColor = bgColor),
                                onClick = {
                                    if (!uiState.isLoading && !uiState.showFeedback) {
                                        selectedAnswer = answer
                                    }
                                }
                            ) {
                                Row(modifier = Modifier.padding(16.dp), verticalAlignment = Alignment.CenterVertically) {
                                    Text(
                                        text = answer, modifier = Modifier.weight(1f),
                                        color = when {
                                            isCorrectAnswer -> Color(0xFF22c55e)
                                            isWrongSelected -> Color(0xFFef4444)
                                            isSelected -> MysticGold
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
                uiState.sessionId == null && !uiState.isLoading -> {
                    Column(
                        modifier = Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Box(
                            modifier = Modifier.size(120.dp).clip(CircleShape)
                                .background(Brush.radialGradient(listOf(MysticPurple.copy(alpha = 0.4f), MysticPurple.copy(alpha = 0.1f), Color.Transparent))),
                            contentAlignment = Alignment.Center
                        ) { Text("\uD83E\uDDE0", fontSize = 64.sp) }

                        Spacer(modifier = Modifier.height(24.dp))
                        Text("Trivia Game", color = MysticGold, fontSize = 28.sp, fontWeight = FontWeight.Bold)
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
                            colors = SliderDefaults.colors(thumbColor = MysticGold, activeTrackColor = MysticGold)
                        )

                        Spacer(modifier = Modifier.height(8.dp))
                        Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp), colors = CardDefaults.cardColors(containerColor = MysticSurface)) {
                            Column(modifier = Modifier.padding(16.dp)) {
                                Text("Cara Bermain:", color = MysticGold, fontWeight = FontWeight.SemiBold)
                                Spacer(modifier = Modifier.height(8.dp))
                                Text("\u2022 Pilih kategori atau kosongkan untuk random", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                Text("\u2022 Jawab pertanyaan dengan benar", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                Text("\u2022 Bangun streak untuk skor lebih tinggi!", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                Text("\u2022 Jawaban benar = +10 poin", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                            }
                        }

                        Spacer(modifier = Modifier.height(24.dp))
                        MysticButton(text = "Mulai Permainan", onClick = { viewModel.startGame(category.ifBlank { null }, questionCount.toInt()) })

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
                            Text("Menyiapkan pertanyaan...", color = TextSecondary)
                        }
                    }
                }
            }
        }
    }
}
