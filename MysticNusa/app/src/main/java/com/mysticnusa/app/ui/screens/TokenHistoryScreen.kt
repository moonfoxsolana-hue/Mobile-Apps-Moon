package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.models.TokenHistoryItem
import com.mysticnusa.app.data.repository.ProfileRepository
import com.mysticnusa.app.ui.components.ErrorMessage
import com.mysticnusa.app.ui.components.LoadingIndicator
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.ProfileViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TokenHistoryScreen(navController: NavController) {
    val viewModel: ProfileViewModel = viewModel(
        factory = ProfileViewModel.Factory(ProfileRepository())
    )
    val uiState by viewModel.uiState.collectAsState()

    LaunchedEffect(Unit) {
        viewModel.loadTokenHistory()
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Riwayat Token", color = MysticGold) },
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
                uiState.isLoading -> LoadingIndicator()
                uiState.error != null -> ErrorMessage(
                    message = uiState.error ?: "Terjadi kesalahan",
                    onRetry = { viewModel.loadTokenHistory() }
                )
                uiState.tokenHistory.isEmpty() -> {
                    Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                        Text("Belum ada riwayat token", color = TextSecondary)
                    }
                }
                else -> {
                    LazyColumn(
                        modifier = Modifier.fillMaxSize(),
                        contentPadding = PaddingValues(16.dp),
                        verticalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        items(uiState.tokenHistory) { item ->
                            TokenHistoryCard(item)
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun TokenHistoryCard(item: TokenHistoryItem) {
    val isAdd = item.action == "add"
    val amountColor = if (isAdd) Color(0xFF22c55e) else Color(0xFFef4444)
    val amountPrefix = if (isAdd) "+" else "-"

    val badgeColor = when (item.type) {
        "airdrop" -> Color(0xFF3b82f6)
        "staking" -> Color(0xFF8b5cf6)
        "games" -> Color(0xFF22c55e)
        else -> MysticPurple
    }

    MysticCard(modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Surface(
                        shape = RoundedCornerShape(4.dp),
                        color = badgeColor.copy(alpha = 0.2f)
                    ) {
                        Text(
                            text = item.type ?: "unknown",
                            color = badgeColor,
                            style = MaterialTheme.typography.labelSmall,
                            modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp)
                        )
                    }
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(
                        text = item.createdAt?.take(10) ?: "",
                        color = TextSecondary,
                        style = MaterialTheme.typography.labelSmall
                    )
                }
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = item.description ?: "",
                    color = TextSecondary,
                    style = MaterialTheme.typography.bodySmall
                )
            }
            Text(
                text = "$amountPrefix${item.amount ?: 0.0}",
                color = amountColor,
                fontWeight = FontWeight.Bold,
                style = MaterialTheme.typography.bodyLarge
            )
        }
    }
}
