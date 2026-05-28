package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
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
import com.mysticnusa.app.data.models.NgepetLobbyMatch
import com.mysticnusa.app.data.repository.GamesRepository
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.NgepetViewModel

enum class NgepetPhase {
    LOBBY, CHOOSE_ITEM, WAITING, RESULT
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun NgepetGameScreen(navController: NavController) {
    val viewModel: NgepetViewModel = viewModel(
        factory = NgepetViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()

    var phase by remember { mutableStateOf(NgepetPhase.LOBBY) }
    var selectedItem by remember { mutableStateOf<String?>(null) }

    val items = listOf(
        "\uD83C\uDFFA Guci", "\uD83D\uDECB Lemari", "\uD83D\uDEBF Kamar Mandi",
        "\uD83D\uDECF Kasur", "\uD83E\uDE91 Kursi", "\uD83D\uDDBC Lukisan",
        "\uD83D\uDCFA TV", "\uD83C\uDF3F Tanaman", "\uD83D\uDCD6 Buku",
        "\uD83D\uDD70 Jam"
    )

    LaunchedEffect(Unit) {
        viewModel.loadMatches()
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Ngepet Online", color = MysticGold) },
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
            when (phase) {
                NgepetPhase.LOBBY -> {
                    Column(modifier = Modifier.fillMaxSize().padding(16.dp)) {
                        Text(
                            text = "\uD83D\uDC17 Lobby Ngepet",
                            color = MysticGold,
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(16.dp))

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
                                    NgepetMatchCard(match = match)
                                }
                            }
                        }

                        uiState.error?.let {
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(text = it, color = MaterialTheme.colorScheme.error)
                        }
                    }
                }

                NgepetPhase.CHOOSE_ITEM -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(16.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        Text(
                            text = "Pilih Tempat Bersembunyi",
                            color = MysticGold,
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Pilih salah satu item untuk menyembunyikan token",
                            color = TextSecondary,
                            textAlign = TextAlign.Center
                        )
                        Spacer(modifier = Modifier.height(16.dp))

                        items.chunked(2).forEach { row ->
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.spacedBy(8.dp)
                            ) {
                                row.forEach { item ->
                                    val isSelected = selectedItem == item
                                    Card(
                                        modifier = Modifier
                                            .weight(1f)
                                            .clickable { selectedItem = item }
                                            .then(
                                                if (isSelected) Modifier.border(2.dp, MysticGold, RoundedCornerShape(12.dp))
                                                else Modifier
                                            ),
                                        shape = RoundedCornerShape(12.dp),
                                        colors = CardDefaults.cardColors(
                                            containerColor = if (isSelected) MysticPurple.copy(alpha = 0.3f) else MysticSurface
                                        )
                                    ) {
                                        Box(
                                            modifier = Modifier
                                                .fillMaxWidth()
                                                .padding(16.dp),
                                            contentAlignment = Alignment.Center
                                        ) {
                                            Text(
                                                text = item,
                                                color = if (isSelected) MysticGold else TextSecondary,
                                                textAlign = TextAlign.Center
                                            )
                                        }
                                    }
                                }
                                if (row.size == 1) {
                                    Spacer(modifier = Modifier.weight(1f))
                                }
                            }
                            Spacer(modifier = Modifier.height(8.dp))
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        if (selectedItem != null) {
                            MysticButton(
                                text = "Konfirmasi",
                                onClick = {
                                    selectedItem?.let { choice ->
                                        viewModel.submitChoice(choice)
                                    }
                                    phase = NgepetPhase.WAITING
                                }
                            )
                        }

                        if (uiState.isLoading) {
                            CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
                        }
                    }
                }

                NgepetPhase.WAITING -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Text("\uD83D\uDC17", fontSize = 64.sp)
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = "Menunggu...",
                            color = MysticGold,
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Menunggu pemain lain membuat tebakan",
                            color = TextSecondary,
                            textAlign = TextAlign.Center
                        )

                        uiState.message?.let {
                            Spacer(modifier = Modifier.height(12.dp))
                            Text(text = it, color = Color(0xFF22c55e))
                        }
                    }
                }

                NgepetPhase.RESULT -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(24.dp),
                        horizontalAlignment = Alignment.CenterHorizontally,
                        verticalArrangement = Arrangement.Center
                    ) {
                        Text(
                            text = uiState.message ?: "Hasil",
                            color = MysticGold,
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Bold,
                            textAlign = TextAlign.Center
                        )
                        Spacer(modifier = Modifier.height(24.dp))

                        MysticButton(
                            text = "Kembali ke Lobby",
                            onClick = {
                                phase = NgepetPhase.LOBBY
                                selectedItem = null
                                viewModel.loadMatches()
                            }
                        )

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
private fun NgepetMatchCard(match: NgepetLobbyMatch) {
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
                    text = match.hostName ?: "Unknown Host",
                    color = MysticGold,
                    fontWeight = FontWeight.Bold
                )
                Text(
                    text = "Intruders: ${match.intrudersCount ?: 0}/${match.maxIntruders ?: 0}",
                    color = TextSecondary,
                    style = MaterialTheme.typography.bodySmall
                )
                Text(
                    text = "Pool: ${match.tokenPool ?: 0} | ${match.difficulty ?: ""}",
                    color = TextSecondary,
                    style = MaterialTheme.typography.bodySmall
                )
                Text(
                    text = "Status: ${match.status ?: "open"}",
                    color = TextSecondary,
                    style = MaterialTheme.typography.bodySmall
                )
            }
        }
    }
}
