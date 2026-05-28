package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.repository.GamesRepository
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticCard
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
    var showFeedback by remember { mutableStateOf(false) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Trivia", color = MysticGold) },
                navigationIcon = {
                    IconButton(onClick = { navController.popBackStack() }) {
                        Icon(Icons.AutoMirrored.Filled.ArrowBack, "Back", tint = MysticGold)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.background
                )
            )
        },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        Box(modifier = Modifier.padding(paddingValues).fillMaxSize()) {
            when {
                // Finished state
                uiState.isComplete && uiState.finishResponse != null -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Text("\uD83C\uDFC6", fontSize = 64.sp)
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = "Permainan Selesai!",
                            color = MysticGold,
                            fontSize = 24.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(24.dp))
                        MysticCard {
                            Column(
                                modifier = Modifier.padding(24.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text("Skor", color = TextSecondary)
                                Text(
                                    text = "${uiState.finishResponse?.score ?: 0}",
                                    color = MysticGold,
                                    fontSize = 48.sp,
                                    fontWeight = FontWeight.Bold
                                )
                                Spacer(modifier = Modifier.height(8.dp))
                                Text("Streak: ${uiState.finishResponse?.streak ?: 0}", color = TextSecondary)
                                Text("Durasi: ${uiState.finishResponse?.durationSeconds ?: 0} detik", color = TextSecondary)
                            }
                        }
                        Spacer(modifier = Modifier.height(24.dp))
                        MysticButton(
                            text = "Main Lagi",
                            onClick = {
                                selectedAnswer = null
                                showFeedback = false
                                viewModel.startGame(
                                    category.ifBlank { null },
                                    questionCount.toInt()
                                )
                            }
                        )
                    }
                }
                // Playing state - needs to finish
                uiState.isComplete && uiState.finishResponse == null -> {
                    LaunchedEffect(Unit) { viewModel.finishGame() }
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        CircularProgressIndicator(color = MysticGold)
                    }
                }
                // Playing state - question displayed
                uiState.currentQuestion != null -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(16.dp)
                    ) {
                        // Progress bar
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Text(
                                text = "Soal ${uiState.currentQuestionNumber}/${uiState.totalQuestions}",
                                color = TextSecondary
                            )
                            Text(
                                text = "Streak: ${uiState.streak} \uD83D\uDD25",
                                color = MysticGold
                            )
                        }
                        Spacer(modifier = Modifier.height(4.dp))
                        LinearProgressIndicator(
                            progress = uiState.currentQuestionNumber.toFloat() / uiState.totalQuestions.coerceAtLeast(1),
                            modifier = Modifier.fillMaxWidth(),
                            color = MysticGold,
                            trackColor = MysticSurface
                        )
                        Spacer(modifier = Modifier.height(24.dp))

                        // Question
                        MysticCard(modifier = Modifier.fillMaxWidth()) {
                            Text(
                                text = uiState.currentQuestion?.question ?: "",
                                color = Color.White,
                                style = MaterialTheme.typography.titleMedium,
                                modifier = Modifier.padding(20.dp)
                            )
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        // Answers
                        uiState.currentQuestion?.answers?.forEach { answer ->
                            val isSelected = selectedAnswer == answer
                            Card(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 4.dp),
                                shape = RoundedCornerShape(12.dp),
                                colors = CardDefaults.cardColors(
                                    containerColor = if (isSelected) MysticPurple.copy(alpha = 0.3f) else MysticSurface
                                ),
                                onClick = {
                                    if (!showFeedback && !uiState.isLoading) {
                                        selectedAnswer = answer
                                    }
                                }
                            ) {
                                Text(
                                    text = answer,
                                    color = if (isSelected) MysticGold else TextSecondary,
                                    modifier = Modifier.padding(16.dp),
                                    style = MaterialTheme.typography.bodyLarge
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        if (selectedAnswer != null && !showFeedback) {
                            MysticButton(
                                text = "Jawab",
                                onClick = {
                                    uiState.currentQuestion?.let { q ->
                                        selectedAnswer?.let { answer ->
                                            viewModel.answerQuestion(q.id, answer)
                                            selectedAnswer = null
                                        }
                                    }
                                },
                                enabled = !uiState.isLoading
                            )
                        }

                        if (uiState.isLoading) {
                            Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                                CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
                            }
                        }
                    }
                }
                // Pre-game state
                uiState.sessionId == null && !uiState.isLoading -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Text("\uD83E\uDDE0", fontSize = 64.sp)
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = "Trivia Game",
                            color = MysticGold,
                            fontSize = 24.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(24.dp))

                        MysticTextField(
                            value = category,
                            onValueChange = { category = it },
                            label = "Kategori (opsional)"
                        )

                        Spacer(modifier = Modifier.height(16.dp))

                        Text("Jumlah Soal: ${questionCount.toInt()}", color = TextSecondary)
                        Slider(
                            value = questionCount,
                            onValueChange = { questionCount = it },
                            valueRange = 5f..50f,
                            steps = 8,
                            colors = SliderDefaults.colors(
                                thumbColor = MysticGold,
                                activeTrackColor = MysticGold
                            )
                        )

                        Spacer(modifier = Modifier.height(24.dp))

                        MysticButton(
                            text = "Mulai",
                            onClick = {
                                viewModel.startGame(
                                    category.ifBlank { null },
                                    questionCount.toInt()
                                )
                            }
                        )

                        uiState.error?.let {
                            Spacer(modifier = Modifier.height(12.dp))
                            Text(text = it, color = MaterialTheme.colorScheme.error)
                        }
                    }
                }
                // Loading
                else -> {
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        CircularProgressIndicator(color = MysticGold)
                    }
                }
            }
        }
    }
}
