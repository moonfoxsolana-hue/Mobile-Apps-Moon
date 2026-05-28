package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
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
import com.mysticnusa.app.data.models.UlartanggaMatch
import com.mysticnusa.app.data.repository.GamesRepository
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.components.MysticTextField
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.UlartanggaViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun UlartanggaGameScreen(navController: NavController) {
    val viewModel: UlartanggaViewModel = viewModel(
        factory = UlartanggaViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()

    var showCreateDialog by remember { mutableStateOf(false) }
    var matchName by remember { mutableStateOf("") }

    LaunchedEffect(Unit) {
        viewModel.loadMatches()
    }

    if (showCreateDialog) {
        AlertDialog(
            onDismissRequest = { showCreateDialog = false },
            title = { Text("Buat Ruangan", color = MysticGold) },
            text = {
                Column {
                    MysticTextField(
                        value = matchName,
                        onValueChange = { matchName = it },
                        label = "Nama Ruangan"
                    )
                }
            },
            confirmButton = {
                TextButton(onClick = {
                    viewModel.createMatch()
                    showCreateDialog = false
                    matchName = ""
                }) {
                    Text("Buat", color = MysticGold)
                }
            },
            dismissButton = {
                TextButton(onClick = { showCreateDialog = false }) {
                    Text("Batal", color = TextSecondary)
                }
            },
            containerColor = MysticSurface
        )
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Ular Tangga", color = MysticGold) },
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
                // Playing state
                uiState.currentMatch != null -> {
                    val match = uiState.currentMatch!!
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(16.dp)
                    ) {
                        // Match info
                        MysticCard(modifier = Modifier.fillMaxWidth()) {
                            Column(modifier = Modifier.padding(16.dp)) {
                                Text("Status: ${match.status ?: "waiting"}", color = MysticGold, fontWeight = FontWeight.Bold)
                                Text("Pemain: ${match.players?.joinToString(", ") ?: "0"}", color = TextSecondary)
                                Text("Giliran: ${match.currentTurn ?: "-"}", color = TextSecondary)
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        // Simple board visualization (10x10 grid)
                        MysticCard(modifier = Modifier.fillMaxWidth()) {
                            Column(modifier = Modifier.padding(8.dp)) {
                                Text(
                                    text = "Papan Permainan",
                                    color = MysticGold,
                                    fontWeight = FontWeight.Bold,
                                    modifier = Modifier.padding(8.dp)
                                )
                                // Simplified 10x10 grid
                                for (row in 9 downTo 0) {
                                    Row(modifier = Modifier.fillMaxWidth()) {
                                        for (col in 0..9) {
                                            val number = if (row % 2 == 0) row * 10 + col + 1 else row * 10 + (9 - col) + 1
                                            val isPlayerHere = uiState.playerPosition == number
                                            Box(
                                                modifier = Modifier
                                                    .weight(1f)
                                                    .aspectRatio(1f)
                                                    .border(0.5.dp, MysticPurple.copy(alpha = 0.3f))
                                                    .background(
                                                        if (isPlayerHere) MysticGold.copy(alpha = 0.3f)
                                                        else Color.Transparent
                                                    ),
                                                contentAlignment = Alignment.Center
                                            ) {
                                                Text(
                                                    text = "$number",
                                                    fontSize = 7.sp,
                                                    color = if (isPlayerHere) MysticGold else TextSecondary.copy(alpha = 0.5f)
                                                )
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        // Dice roll
                        MysticButton(
                            text = if (uiState.lastDice != null) "Lempar Dadu (Terakhir: ${uiState.lastDice})" else "Lempar Dadu",
                            onClick = { viewModel.throwDice(match.id) },
                            enabled = !uiState.isLoading
                        )

                        // Event log
                        uiState.lastEvent?.let { event ->
                            Spacer(modifier = Modifier.height(12.dp))
                            MysticCard(modifier = Modifier.fillMaxWidth()) {
                                Text(
                                    text = event,
                                    color = MysticPurpleLight,
                                    modifier = Modifier.padding(12.dp),
                                    style = MaterialTheme.typography.bodySmall
                                )
                            }
                        }

                        uiState.error?.let {
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(text = it, color = MaterialTheme.colorScheme.error)
                        }
                    }
                }
                // Loading
                uiState.isLoading -> {
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        CircularProgressIndicator(color = MysticGold)
                    }
                }
                // Lobby
                else -> {
                    Column(modifier = Modifier.fillMaxSize().padding(16.dp)) {
                        Text(
                            text = "\uD83C\uDFB2 Lobby Ular Tangga",
                            color = MysticGold,
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(16.dp))

                        MysticButton(
                            text = "Buat Ruangan",
                            onClick = { showCreateDialog = true }
                        )

                        Spacer(modifier = Modifier.height(16.dp))

                        if (uiState.matches.isEmpty()) {
                            Box(
                                modifier = Modifier.fillMaxWidth().padding(32.dp),
                                contentAlignment = Alignment.Center
                            ) {
                                Text("Belum ada ruangan tersedia", color = TextSecondary)
                            }
                        } else {
                            LazyColumn(
                                verticalArrangement = Arrangement.spacedBy(8.dp)
                            ) {
                                items(uiState.matches) { match ->
                                    MatchCard(
                                        match = match,
                                        onJoin = { viewModel.joinMatch(match.id) }
                                    )
                                }
                            }
                        }

                        uiState.error?.let {
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(text = it, color = MaterialTheme.colorScheme.error)
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun MatchCard(match: UlartanggaMatch, onJoin: () -> Unit) {
    MysticCard(modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column {
                Text(
                    text = "Ruangan #${match.id}",
                    color = MysticGold,
                    fontWeight = FontWeight.Bold
                )
                Text(
                    text = "Pemain: ${match.players?.size ?: 0}",
                    color = TextSecondary,
                    style = MaterialTheme.typography.bodySmall
                )
                Text(
                    text = "Status: ${match.status ?: "waiting"}",
                    color = TextSecondary,
                    style = MaterialTheme.typography.bodySmall
                )
            }
            Button(
                onClick = onJoin,
                colors = ButtonDefaults.buttonColors(containerColor = MysticGold),
                contentPadding = PaddingValues(horizontal = 16.dp, vertical = 4.dp)
            ) {
                Text("Gabung", color = Color.Black, style = MaterialTheme.typography.labelSmall)
            }
        }
    }
}
