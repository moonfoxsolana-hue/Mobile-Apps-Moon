package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
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
    var selectedItemId by remember { mutableStateOf<Int?>(null) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Intuition", color = MysticGold) },
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
                uiState.isComplete -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Text("\uD83D\uDD2E", fontSize = 64.sp)
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
                                Text("Skor Benar", color = TextSecondary)
                                Text(
                                    text = "${uiState.score}/${uiState.totalRounds}",
                                    color = MysticGold,
                                    fontSize = 48.sp,
                                    fontWeight = FontWeight.Bold
                                )
                            }
                        }
                        Spacer(modifier = Modifier.height(24.dp))
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
                        Text(
                            text = "Ronde ${uiState.currentRound}/${uiState.totalRounds}",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodyMedium
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        LinearProgressIndicator(
                            progress = uiState.currentRound.toFloat() / uiState.totalRounds.coerceAtLeast(1),
                            modifier = Modifier.fillMaxWidth(),
                            color = MysticGold,
                            trackColor = MysticSurface
                        )
                        Spacer(modifier = Modifier.height(16.dp))

                        Text(
                            text = "Pilih satu item yang kamu rasa benar:",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodyMedium
                        )
                        Spacer(modifier = Modifier.height(16.dp))

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            uiState.items.forEach { item ->
                                ItemCard(
                                    item = item,
                                    isSelected = selectedItemId == item.id,
                                    onClick = { selectedItemId = item.id },
                                    modifier = Modifier.weight(1f)
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(24.dp))

                        if (selectedItemId != null) {
                            MysticButton(
                                text = "Pilih",
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
                // Pre-game
                uiState.matchId == null && !uiState.isLoading -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Text("\uD83D\uDD2E", fontSize = 64.sp)
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = "Intuition Game",
                            color = MysticGold,
                            fontSize = 24.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(12.dp))
                        Text(
                            text = "Ikuti intuisimu! Pilih item yang benar dari 3 pilihan selama 10 ronde.",
                            color = TextSecondary,
                            textAlign = TextAlign.Center,
                            style = MaterialTheme.typography.bodyMedium
                        )
                        Spacer(modifier = Modifier.height(32.dp))
                        MysticButton(
                            text = "Mulai",
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

@Composable
private fun ItemCard(
    item: IntuitionRoundItem,
    isSelected: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Card(
        modifier = modifier
            .clickable { onClick() }
            .then(
                if (isSelected) Modifier.border(2.dp, MysticGold, RoundedCornerShape(12.dp))
                else Modifier
            ),
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(
            containerColor = if (isSelected) MysticPurple.copy(alpha = 0.3f) else MysticSurface
        )
    ) {
        Column(
            modifier = Modifier.padding(12.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            item.image?.let { imageUrl ->
                AsyncImage(
                    model = imageUrl,
                    contentDescription = item.name,
                    modifier = Modifier
                        .size(80.dp)
                        .clip(RoundedCornerShape(8.dp)),
                    contentScale = ContentScale.Crop
                )
                Spacer(modifier = Modifier.height(8.dp))
            }
            Text(
                text = item.name ?: "",
                color = if (isSelected) MysticGold else TextSecondary,
                style = MaterialTheme.typography.bodySmall,
                textAlign = TextAlign.Center
            )
        }
    }
}
