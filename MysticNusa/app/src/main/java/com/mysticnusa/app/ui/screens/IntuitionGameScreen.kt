package com.mysticnusa.app.ui.screens

import androidx.compose.animation.*
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
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
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import coil.compose.AsyncImage
import com.mysticnusa.app.data.models.IntuitionRoundItem
import com.mysticnusa.app.data.repository.GamesRepository
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.IntuitionViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun IntuitionGameScreen(navController: NavController) {
    val viewModel: IntuitionViewModel = viewModel(
        factory = IntuitionViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()
    var selectedItemId by remember { mutableStateOf<String?>(null) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Intuition Game", color = MysticGold) },
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
                // Game Complete
                uiState.isComplete -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        // Trophy icon
                        Box(
                            modifier = Modifier
                                .size(100.dp)
                                .clip(CircleShape)
                                .background(
                                    Brush.radialGradient(
                                        colors = listOf(MysticGold.copy(alpha = 0.3f), Color.Transparent)
                                    )
                                ),
                            contentAlignment = Alignment.Center
                        ) {
                            Text("\uD83C\uDFC6", fontSize = 56.sp)
                        }

                        Spacer(modifier = Modifier.height(24.dp))
                        Text(
                            text = "Permainan Selesai!",
                            color = MysticGold,
                            fontSize = 26.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Intuisimu luar biasa!",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodyMedium
                        )

                        Spacer(modifier = Modifier.height(32.dp))

                        // Score card
                        Card(
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(20.dp),
                            colors = CardDefaults.cardColors(containerColor = MysticSurface)
                        ) {
                            Column(
                                modifier = Modifier.padding(32.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Text("Skor Akhir", color = TextSecondary, style = MaterialTheme.typography.bodyMedium)
                                Spacer(modifier = Modifier.height(8.dp))
                                Text(
                                    text = "${uiState.score}",
                                    color = MysticGold,
                                    fontSize = 64.sp,
                                    fontWeight = FontWeight.Bold
                                )
                                Text(
                                    text = "dari ${uiState.totalRounds} ronde",
                                    color = TextSecondary,
                                    style = MaterialTheme.typography.bodySmall
                                )
                                Spacer(modifier = Modifier.height(16.dp))

                                // Accuracy bar
                                val accuracy = if (uiState.totalRounds > 0) uiState.score.toFloat() / uiState.totalRounds else 0f
                                LinearProgressIndicator(
                                    progress = { accuracy },
                                    modifier = Modifier.fillMaxWidth().height(8.dp).clip(RoundedCornerShape(4.dp)),
                                    color = when {
                                        accuracy >= 0.8f -> Color(0xFF22c55e)
                                        accuracy >= 0.5f -> MysticGold
                                        else -> Color(0xFFef4444)
                                    },
                                    trackColor = MysticPurple.copy(alpha = 0.2f)
                                )
                                Spacer(modifier = Modifier.height(8.dp))
                                Text(
                                    text = "Akurasi: ${(accuracy * 100).toInt()}%",
                                    color = TextSecondary,
                                    style = MaterialTheme.typography.bodySmall
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(32.dp))

                        MysticButton(
                            text = "Main Lagi",
                            onClick = {
                                selectedItemId = null
                                viewModel.startGame()
                            }
                        )
                    }
                }

                // Playing - items shown
                uiState.matchId != null && uiState.items.isNotEmpty() -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(16.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        // Header with round info and score
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = "Ronde ${uiState.currentRound}/${uiState.totalRounds}",
                                color = TextSecondary,
                                style = MaterialTheme.typography.bodyMedium
                            )
                            // Score badge
                            Surface(
                                shape = RoundedCornerShape(20.dp),
                                color = MysticGold.copy(alpha = 0.15f)
                            ) {
                                Text(
                                    text = "\u2B50 ${uiState.score}",
                                    color = MysticGold,
                                    fontWeight = FontWeight.Bold,
                                    modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp),
                                    style = MaterialTheme.typography.bodyMedium
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(8.dp))

                        // Progress bar
                        LinearProgressIndicator(
                            progress = { uiState.currentRound.toFloat() / uiState.totalRounds.coerceAtLeast(1) },
                            modifier = Modifier.fillMaxWidth().height(6.dp).clip(RoundedCornerShape(3.dp)),
                            color = MysticGold,
                            trackColor = MysticSurface
                        )

                        Spacer(modifier = Modifier.height(24.dp))

                        // Feedback indicator
                        if (uiState.showFeedback && uiState.lastAnswerCorrect != null) {
                            Surface(
                                shape = RoundedCornerShape(12.dp),
                                color = if (uiState.lastAnswerCorrect == true) Color(0xFF22c55e).copy(alpha = 0.15f) else Color(0xFFef4444).copy(alpha = 0.15f)
                            ) {
                                Row(
                                    modifier = Modifier.padding(horizontal = 16.dp, vertical = 8.dp),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Icon(
                                        imageVector = if (uiState.lastAnswerCorrect == true) Icons.Default.CheckCircle else Icons.Default.Close,
                                        contentDescription = null,
                                        tint = if (uiState.lastAnswerCorrect == true) Color(0xFF22c55e) else Color(0xFFef4444),
                                        modifier = Modifier.size(20.dp)
                                    )
                                    Spacer(modifier = Modifier.width(8.dp))
                                    Text(
                                        text = if (uiState.lastAnswerCorrect == true) "Benar! +1" else "Salah!",
                                        color = if (uiState.lastAnswerCorrect == true) Color(0xFF22c55e) else Color(0xFFef4444),
                                        fontWeight = FontWeight.Medium
                                    )
                                }
                            }
                            Spacer(modifier = Modifier.height(16.dp))
                        }

                        // Question prompt
                        Text(
                            text = "\uD83D\uDD2E Gunakan intuisimu!",
                            color = MysticGold,
                            fontSize = 18.sp,
                            fontWeight = FontWeight.SemiBold
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = "Pilih satu item yang kamu rasa benar",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodyMedium
                        )

                        Spacer(modifier = Modifier.height(24.dp))

                        // Item cards - vertical layout
                        Column(
                            modifier = Modifier.fillMaxWidth(),
                            verticalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            uiState.items.forEach { item ->
                                IntuitionItemCard(
                                    item = item,
                                    isSelected = selectedItemId == item.id,
                                    onClick = {
                                        if (!uiState.isLoading && !uiState.showFeedback) {
                                            selectedItemId = item.id
                                        }
                                    }
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(24.dp))

                        // Submit button
                        if (selectedItemId != null && !uiState.showFeedback) {
                            MysticButton(
                                text = "Pilih Jawaban",
                                onClick = {
                                    selectedItemId?.let {
                                        viewModel.answerRound(it)
                                        selectedItemId = null
                                    }
                                },
                                enabled = !uiState.isLoading
                            )
                        }

                        if (uiState.isLoading) {
                            Spacer(modifier = Modifier.height(16.dp))
                            CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
                        }
                    }
                }

                // Pre-game / Start screen
                uiState.matchId == null && !uiState.isLoading -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        // Mystic orb
                        Box(
                            modifier = Modifier
                                .size(120.dp)
                                .clip(CircleShape)
                                .background(
                                    Brush.radialGradient(
                                        colors = listOf(
                                            MysticPurple.copy(alpha = 0.4f),
                                            MysticPurple.copy(alpha = 0.1f),
                                            Color.Transparent
                                        )
                                    )
                                ),
                            contentAlignment = Alignment.Center
                        ) {
                            Text("\uD83D\uDD2E", fontSize = 64.sp)
                        }

                        Spacer(modifier = Modifier.height(24.dp))
                        Text(
                            text = "Intuition Game",
                            color = MysticGold,
                            fontSize = 28.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(12.dp))
                        Text(
                            text = "Percaya pada intuisimu! Pilih item yang benar dari 3 pilihan selama 10 ronde. Semakin tajam instingmu, semakin tinggi skormu.",
                            color = TextSecondary,
                            textAlign = TextAlign.Center,
                            style = MaterialTheme.typography.bodyMedium
                        )

                        Spacer(modifier = Modifier.height(16.dp))

                        // Game rules
                        Card(
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(16.dp),
                            colors = CardDefaults.cardColors(containerColor = MysticSurface)
                        ) {
                            Column(modifier = Modifier.padding(16.dp)) {
                                Text("Cara Bermain:", color = MysticGold, fontWeight = FontWeight.SemiBold)
                                Spacer(modifier = Modifier.height(8.dp))
                                Text("\u2022 10 ronde, 3 pilihan per ronde", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                Text("\u2022 Pilih 1 item yang menurutmu benar", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                Text("\u2022 Jawaban benar = +10 token reward", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                                Text("\u2022 Naik level setiap 100 poin!", color = TextSecondary, style = MaterialTheme.typography.bodySmall)
                            }
                        }

                        Spacer(modifier = Modifier.height(32.dp))

                        MysticButton(
                            text = "Mulai Permainan",
                            onClick = { viewModel.startGame() }
                        )

                        uiState.error?.let {
                            Spacer(modifier = Modifier.height(12.dp))
                            Card(
                                shape = RoundedCornerShape(8.dp),
                                colors = CardDefaults.cardColors(containerColor = Color(0xFFef4444).copy(alpha = 0.1f))
                            ) {
                                Text(
                                    text = it,
                                    color = Color(0xFFef4444),
                                    style = MaterialTheme.typography.bodySmall,
                                    modifier = Modifier.padding(12.dp)
                                )
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
                            Text("Mempersiapkan permainan...", color = TextSecondary)
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun IntuitionItemCard(
    item: IntuitionRoundItem,
    isSelected: Boolean,
    onClick: () -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() }
            .then(
                if (isSelected) Modifier.border(2.dp, MysticGold, RoundedCornerShape(16.dp))
                else Modifier
            ),
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(
            containerColor = if (isSelected) MysticPurple.copy(alpha = 0.2f) else MysticSurface
        ),
        elevation = CardDefaults.cardElevation(defaultElevation = if (isSelected) 8.dp else 2.dp)
    ) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Image or placeholder
            Box(
                modifier = Modifier
                    .size(60.dp)
                    .clip(RoundedCornerShape(12.dp))
                    .background(MysticPurple.copy(alpha = 0.2f)),
                contentAlignment = Alignment.Center
            ) {
                if (item.image != null) {
                    AsyncImage(
                        model = item.image,
                        contentDescription = item.name,
                        modifier = Modifier.fillMaxSize().clip(RoundedCornerShape(12.dp)),
                        contentScale = ContentScale.Crop
                    )
                } else {
                    Text("\uD83D\uDD2E", fontSize = 28.sp)
                }
            }

            Spacer(modifier = Modifier.width(16.dp))

            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = item.name ?: "???",
                    color = if (isSelected) MysticGold else Color.White,
                    fontWeight = FontWeight.Medium,
                    style = MaterialTheme.typography.titleMedium
                )
                item.description?.let { desc ->
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = desc,
                        color = TextSecondary,
                        style = MaterialTheme.typography.bodySmall,
                        maxLines = 2
                    )
                }
            }

            if (isSelected) {
                Icon(
                    imageVector = Icons.Default.CheckCircle,
                    contentDescription = "Selected",
                    tint = MysticGold,
                    modifier = Modifier.size(24.dp)
                )
            }
        }
    }
}
