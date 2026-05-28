package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
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
import com.mysticnusa.app.ui.viewmodels.TarotViewModel

enum class TarotPhase {
    START, CARD_SELECTION, DETAIL_INPUT, ORACLE, READING
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TarotGameScreen(navController: NavController) {
    val viewModel: TarotViewModel = viewModel(
        factory = TarotViewModel.Factory(GamesRepository())
    )
    val uiState by viewModel.uiState.collectAsState()

    var phase by remember { mutableStateOf(TarotPhase.START) }
    var selectedCardIds by remember { mutableStateOf(setOf<String>()) }
    var name by remember { mutableStateOf("") }
    var energyChoice by remember { mutableStateOf("") }

    val energyOptions = listOf("Api", "Air", "Tanah", "Udara", "Cahaya", "Kegelapan")

    // Advance phases based on state
    LaunchedEffect(uiState.sessionId, uiState.cards) {
        if (uiState.sessionId != null && uiState.cards.isNotEmpty() && phase == TarotPhase.START) {
            phase = TarotPhase.CARD_SELECTION
        }
    }

    LaunchedEffect(uiState.reading) {
        if (uiState.reading != null) {
            phase = TarotPhase.READING
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Tarot Ritual", color = MysticGold) },
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
            if (uiState.isLoading) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        CircularProgressIndicator(color = MysticGold)
                        Spacer(modifier = Modifier.height(12.dp))
                        Text("Membaca energi kosmis...", color = TextSecondary)
                    }
                }
            } else {
                when (phase) {
                    TarotPhase.START -> {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center
                        ) {
                            Text("\uD83C\uDCCF", fontSize = 64.sp)
                            Spacer(modifier = Modifier.height(16.dp))
                            Text(
                                text = "Tarot Ritual",
                                color = MysticGold,
                                fontSize = 24.sp,
                                fontWeight = FontWeight.Bold
                            )
                            Spacer(modifier = Modifier.height(12.dp))
                            Text(
                                text = "Baca takdirmu hari ini melalui kartu tarot mistis. Pilih kartu, masukkan energimu, dan terima ramalan dari oracle.",
                                color = TextSecondary,
                                textAlign = TextAlign.Center,
                                style = MaterialTheme.typography.bodyMedium
                            )
                            Spacer(modifier = Modifier.height(32.dp))
                            MysticButton(
                                text = "Mulai Ritual",
                                onClick = { viewModel.startRitual() }
                            )

                            uiState.error?.let {
                                Spacer(modifier = Modifier.height(12.dp))
                                Text(text = it, color = MaterialTheme.colorScheme.error)
                            }
                        }
                    }

                    TarotPhase.CARD_SELECTION -> {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .verticalScroll(rememberScrollState())
                                .padding(16.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text(
                                text = "Pilih 1-3 Kartu",
                                color = MysticGold,
                                fontSize = 20.sp,
                                fontWeight = FontWeight.Bold
                            )
                            Spacer(modifier = Modifier.height(8.dp))
                            Text(
                                text = "Terpilih: ${selectedCardIds.size}/3",
                                color = TextSecondary
                            )
                            Spacer(modifier = Modifier.height(16.dp))

                            // Card grid - 5 columns x 2 rows
                            uiState.cards.chunked(5).forEach { row ->
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.spacedBy(6.dp)
                                ) {
                                    row.forEach { card ->
                                        val isSelected = card.id in selectedCardIds
                                        Card(
                                            modifier = Modifier
                                                .weight(1f)
                                                .aspectRatio(0.65f)
                                                .clickable {
                                                    selectedCardIds = if (isSelected) {
                                                        selectedCardIds - card.id
                                                    } else if (selectedCardIds.size < 3) {
                                                        selectedCardIds + card.id
                                                    } else {
                                                        selectedCardIds
                                                    }
                                                }
                                                .then(
                                                    if (isSelected) Modifier.border(2.dp, MysticGold, RoundedCornerShape(8.dp))
                                                    else Modifier
                                                ),
                                            shape = RoundedCornerShape(8.dp),
                                            colors = CardDefaults.cardColors(
                                                containerColor = if (isSelected) MysticPurple.copy(alpha = 0.4f) else MysticPurple.copy(alpha = 0.7f)
                                            )
                                        ) {
                                            Box(
                                                modifier = Modifier.fillMaxSize(),
                                                contentAlignment = Alignment.Center
                                            ) {
                                                Text(
                                                    text = if (isSelected) "\u2728" else "\uD83C\uDCCF",
                                                    fontSize = 24.sp
                                                )
                                            }
                                        }
                                    }
                                }
                                Spacer(modifier = Modifier.height(6.dp))
                            }

                            Spacer(modifier = Modifier.height(24.dp))

                            if (selectedCardIds.isNotEmpty()) {
                                MysticButton(
                                    text = "Lanjut",
                                    onClick = { phase = TarotPhase.DETAIL_INPUT }
                                )
                            }
                        }
                    }

                    TarotPhase.DETAIL_INPUT -> {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .verticalScroll(rememberScrollState())
                                .padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text(
                                text = "Masukkan Detail",
                                color = MysticGold,
                                fontSize = 20.sp,
                                fontWeight = FontWeight.Bold
                            )
                            Spacer(modifier = Modifier.height(24.dp))

                            MysticTextField(
                                value = name,
                                onValueChange = { name = it },
                                label = "Nama"
                            )

                            Spacer(modifier = Modifier.height(16.dp))

                            Text("Pilih Energi:", color = TextSecondary)
                            Spacer(modifier = Modifier.height(8.dp))

                            Column {
                                energyOptions.chunked(3).forEach { row ->
                                    Row(
                                        modifier = Modifier.fillMaxWidth(),
                                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                                    ) {
                                        row.forEach { energy ->
                                            val isSelected = energyChoice == energy
                                            FilterChip(
                                                selected = isSelected,
                                                onClick = { energyChoice = energy },
                                                label = { Text(energy) },
                                                colors = FilterChipDefaults.filterChipColors(
                                                    selectedContainerColor = MysticGold.copy(alpha = 0.2f),
                                                    selectedLabelColor = MysticGold
                                                ),
                                                modifier = Modifier.weight(1f)
                                            )
                                        }
                                    }
                                    Spacer(modifier = Modifier.height(4.dp))
                                }
                            }

                            Spacer(modifier = Modifier.height(24.dp))

                            MysticButton(
                                text = "Kirim",
                                onClick = {
                                    val cardSelections = uiState.cards
                                        .filter { it.id in selectedCardIds }
                                        .map { com.mysticnusa.app.data.models.TarotCardSelection(it.id, it.orientation ?: "upright") }
                                    viewModel.pickCards(
                                        name.ifBlank { null },
                                        energyChoice.ifBlank { null },
                                        cardSelections
                                    )
                                    phase = TarotPhase.ORACLE
                                },
                                enabled = name.isNotBlank()
                            )
                        }
                    }

                    TarotPhase.ORACLE -> {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .verticalScroll(rememberScrollState())
                                .padding(24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center
                        ) {
                            Text("\uD83C\uDCCF", fontSize = 48.sp)
                            Spacer(modifier = Modifier.height(16.dp))

                            // Oracle info
                            uiState.oracleName?.let { oracle ->
                                Text(
                                    text = oracle,
                                    color = MysticPurpleLight,
                                    fontSize = 18.sp,
                                    fontWeight = FontWeight.SemiBold
                                )
                                Spacer(modifier = Modifier.height(8.dp))
                            }
                            uiState.oracleMessage?.let { message ->
                                Text(
                                    text = "\"$message\"",
                                    color = TextSecondary,
                                    style = MaterialTheme.typography.bodyMedium.copy(
                                        fontStyle = androidx.compose.ui.text.font.FontStyle.Italic
                                    ),
                                    textAlign = TextAlign.Center
                                )
                                Spacer(modifier = Modifier.height(16.dp))
                            }

                            Text(
                                text = "Kartu Terpilih",
                                color = MysticGold,
                                fontSize = 20.sp,
                                fontWeight = FontWeight.Bold
                            )
                            Spacer(modifier = Modifier.height(12.dp))
                            Text(
                                text = "${selectedCardIds.size} kartu telah dipilih",
                                color = TextSecondary
                            )
                            Spacer(modifier = Modifier.height(24.dp))

                            uiState.cardDetails.forEach { card ->
                                MysticCard(modifier = Modifier.fillMaxWidth()) {
                                    Column(modifier = Modifier.padding(12.dp)) {
                                        Text(
                                            text = card.name ?: "Kartu",
                                            color = MysticGold,
                                            fontWeight = FontWeight.Bold
                                        )
                                        Text(
                                            text = card.orientation ?: "",
                                            color = MysticPurpleLight,
                                            style = MaterialTheme.typography.labelSmall
                                        )
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(24.dp))

                            MysticButton(
                                text = "Minta Ramalan AI",
                                onClick = { viewModel.getReading() }
                            )

                            uiState.error?.let {
                                Spacer(modifier = Modifier.height(12.dp))
                                Text(text = it, color = MaterialTheme.colorScheme.error)
                            }
                        }
                    }

                    TarotPhase.READING -> {
                        Column(
                            modifier = Modifier
                                .fillMaxSize()
                                .verticalScroll(rememberScrollState())
                                .padding(16.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text("\u2728", fontSize = 48.sp)
                            Spacer(modifier = Modifier.height(12.dp))
                            Text(
                                text = "Ramalan Tarot",
                                color = MysticGold,
                                fontSize = 22.sp,
                                fontWeight = FontWeight.Bold
                            )
                            Spacer(modifier = Modifier.height(16.dp))

                            uiState.cardDetails.forEach { card ->
                                MysticCard(modifier = Modifier.fillMaxWidth()) {
                                    Column(modifier = Modifier.padding(12.dp)) {
                                        Row(
                                            modifier = Modifier.fillMaxWidth(),
                                            horizontalArrangement = Arrangement.SpaceBetween
                                        ) {
                                            Text(
                                                text = card.name ?: "",
                                                color = MysticGold,
                                                fontWeight = FontWeight.Bold
                                            )
                                            Surface(
                                                shape = RoundedCornerShape(4.dp),
                                                color = MysticPurple.copy(alpha = 0.3f)
                                            ) {
                                                Text(
                                                    text = card.orientation ?: "",
                                                    color = MysticPurpleLight,
                                                    style = MaterialTheme.typography.labelSmall,
                                                    modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                                                )
                                            }
                                        }
                                    }
                                }
                            }

                            Spacer(modifier = Modifier.height(16.dp))

                            MysticCard(modifier = Modifier.fillMaxWidth()) {
                                Text(
                                    text = uiState.reading ?: "",
                                    color = TextSecondary,
                                    style = MaterialTheme.typography.bodyMedium,
                                    modifier = Modifier.padding(16.dp)
                                )
                            }

                            Spacer(modifier = Modifier.height(24.dp))

                            MysticButton(
                                text = "Ritual Baru",
                                onClick = {
                                    phase = TarotPhase.START
                                    selectedCardIds = emptySet()
                                    name = ""
                                    energyChoice = ""
                                    viewModel.startRitual()
                                }
                            )
                        }
                    }
                }
            }
        }
    }
}
