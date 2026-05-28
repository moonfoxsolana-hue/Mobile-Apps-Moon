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

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Logical / IQ Test", color = MysticGold) },
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
                // Finished
                uiState.isComplete && uiState.finishResponse != null -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Text("\uD83E\uDDEA", fontSize = 64.sp)
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = "Tes Selesai!",
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
                                Text("IQ Score", color = TextSecondary)
                                Text(
                                    text = "${uiState.finishResponse?.iq ?: 0}",
                                    color = MysticGold,
                                    fontSize = 56.sp,
                                    fontWeight = FontWeight.Bold
                                )
                                Spacer(modifier = Modifier.height(8.dp))
                                Text(
                                    text = uiState.finishResponse?.category ?: "",
                                    color = MysticPurpleLight,
                                    fontWeight = FontWeight.Medium,
                                    style = MaterialTheme.typography.titleMedium
                                )
                                Spacer(modifier = Modifier.height(8.dp))
                                Text(
                                    text = uiState.finishResponse?.message ?: "",
                                    color = TextSecondary,
                                    textAlign = TextAlign.Center,
                                    style = MaterialTheme.typography.bodyMedium
                                )
                                Spacer(modifier = Modifier.height(12.dp))
                                Divider(color = MysticPurple.copy(alpha = 0.3f))
                                Spacer(modifier = Modifier.height(12.dp))
                                Text("Total Poin: ${uiState.finishResponse?.totalPoint ?: 0}", color = TextSecondary)
                                Text("Durasi: ${uiState.finishResponse?.durationSeconds ?: 0} detik", color = TextSecondary)
                            }
                        }

                        Spacer(modifier = Modifier.height(24.dp))
                        MysticButton(
                            text = "Main Lagi",
                            onClick = {
                                selectedAnswerId = null
                                viewModel.startGame()
                            }
                        )
                    }
                }
                // Auto-finish
                uiState.isComplete && uiState.finishResponse == null -> {
                    LaunchedEffect(Unit) { viewModel.finishGame() }
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        CircularProgressIndicator(color = MysticGold)
                    }
                }
                // Playing
                uiState.currentQuestion != null -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(16.dp)
                    ) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween
                        ) {
                            Text(
                                text = "Soal ${uiState.currentQuestionNumber}/${uiState.totalQuestions}",
                                color = TextSecondary
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

                        MysticCard(modifier = Modifier.fillMaxWidth()) {
                            Text(
                                text = uiState.currentQuestion?.question ?: "",
                                color = Color.White,
                                style = MaterialTheme.typography.titleMedium,
                                modifier = Modifier.padding(20.dp)
                            )
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        uiState.currentQuestion?.answers?.forEach { answer ->
                            val isSelected = selectedAnswerId == answer.id
                            Card(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 4.dp),
                                shape = RoundedCornerShape(12.dp),
                                colors = CardDefaults.cardColors(
                                    containerColor = if (isSelected) MysticPurple.copy(alpha = 0.3f) else MysticSurface
                                ),
                                onClick = {
                                    if (!uiState.isLoading) {
                                        selectedAnswerId = answer.id
                                    }
                                }
                            ) {
                                Text(
                                    text = answer.text ?: "",
                                    color = if (isSelected) MysticGold else TextSecondary,
                                    modifier = Modifier.padding(16.dp),
                                    style = MaterialTheme.typography.bodyLarge
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        if (selectedAnswerId != null) {
                            MysticButton(
                                text = "Jawab",
                                onClick = {
                                    uiState.currentQuestion?.let { q ->
                                        selectedAnswerId?.let { answerId ->
                                            viewModel.answerQuestion(q.id, answerId)
                                            selectedAnswerId = null
                                        }
                                    }
                                },
                                enabled = !uiState.isLoading
                            )
                        }

                        if (uiState.isLoading) {
                            Box(modifier = Modifier.fillMaxWidth().padding(16.dp), contentAlignment = Alignment.Center) {
                                CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
                            }
                        }
                    }
                }
                // Pre-game
                uiState.matchId == null && !uiState.isLoading -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Text("\uD83E\uDDEA", fontSize = 64.sp)
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = "Tes Logika / IQ",
                            color = MysticGold,
                            fontSize = 24.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(12.dp))
                        Text(
                            text = "Jawab pertanyaan logika untuk mengukur IQ-mu. Terdiri dari 10 soal dengan berbagai tingkat kesulitan.",
                            color = TextSecondary,
                            textAlign = TextAlign.Center,
                            style = MaterialTheme.typography.bodyMedium
                        )
                        Spacer(modifier = Modifier.height(32.dp))
                        MysticButton(
                            text = "Mulai Tes IQ",
                            onClick = { viewModel.startGame() }
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
