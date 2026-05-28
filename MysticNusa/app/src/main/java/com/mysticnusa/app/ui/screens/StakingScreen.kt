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
import com.mysticnusa.app.data.models.StakingType
import com.mysticnusa.app.data.models.UserStaking
import com.mysticnusa.app.data.repository.StakingRepository
import com.mysticnusa.app.ui.components.ErrorMessage
import com.mysticnusa.app.ui.components.LoadingIndicator
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.StakingViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StakingScreen(navController: NavController) {
    val viewModel: StakingViewModel = viewModel(
        factory = StakingViewModel.Factory(StakingRepository())
    )
    val uiState by viewModel.uiState.collectAsState()
    var selectedTab by remember { mutableIntStateOf(0) }
    var showStakeDialog by remember { mutableStateOf(false) }
    var selectedTypeId by remember { mutableIntStateOf(0) }
    var selectedDurationId by remember { mutableIntStateOf(0) }
    var selectedTypeName by remember { mutableStateOf("") }
    var selectedDurationDays by remember { mutableIntStateOf(0) }

    LaunchedEffect(Unit) {
        viewModel.loadStakingTypes()
        viewModel.loadUserStakings()
    }

    if (showStakeDialog) {
        AlertDialog(
            onDismissRequest = { showStakeDialog = false },
            title = { Text("Konfirmasi Staking", color = MysticGold) },
            text = {
                Column {
                    Text("Paket: $selectedTypeName", color = TextSecondary)
                    Text("Durasi: $selectedDurationDays hari", color = TextSecondary)
                    Spacer(modifier = Modifier.height(8.dp))
                    Text("Yakin ingin melakukan staking?", color = TextSecondary)
                }
            },
            confirmButton = {
                TextButton(onClick = {
                    viewModel.stake(selectedTypeId, selectedDurationId)
                    showStakeDialog = false
                }) {
                    Text("Konfirmasi", color = MysticGold)
                }
            },
            dismissButton = {
                TextButton(onClick = { showStakeDialog = false }) {
                    Text("Batal", color = TextSecondary)
                }
            },
            containerColor = MysticSurface
        )
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Staking", color = MysticGold) },
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
        Column(modifier = Modifier.padding(paddingValues).fillMaxSize()) {
            TabRow(
                selectedTabIndex = selectedTab,
                containerColor = MysticSurface,
                contentColor = MysticGold
            ) {
                Tab(
                    selected = selectedTab == 0,
                    onClick = { selectedTab = 0 },
                    text = { Text("Paket Staking") }
                )
                Tab(
                    selected = selectedTab == 1,
                    onClick = { selectedTab = 1 },
                    text = { Text("Staking Saya") }
                )
            }

            uiState.message?.let { message ->
                Surface(
                    color = Color(0xFF22c55e).copy(alpha = 0.1f),
                    modifier = Modifier.fillMaxWidth().padding(8.dp),
                    shape = RoundedCornerShape(8.dp)
                ) {
                    Text(
                        text = message,
                        color = Color(0xFF22c55e),
                        modifier = Modifier.padding(12.dp),
                        style = MaterialTheme.typography.bodySmall
                    )
                }
            }

            when {
                uiState.isLoading -> LoadingIndicator()
                uiState.error != null -> ErrorMessage(
                    message = uiState.error ?: "Terjadi kesalahan",
                    onRetry = {
                        viewModel.loadStakingTypes()
                        viewModel.loadUserStakings()
                    }
                )
                else -> {
                    when (selectedTab) {
                        0 -> StakingTypesList(
                            types = uiState.stakingTypes,
                            onStake = { typeId, durationId, typeName, days ->
                                selectedTypeId = typeId
                                selectedDurationId = durationId
                                selectedTypeName = typeName
                                selectedDurationDays = days
                                showStakeDialog = true
                            }
                        )
                        1 -> UserStakingsList(
                            stakings = uiState.userStakings,
                            onClaim = { viewModel.claimReward(it) },
                            onCancel = { viewModel.cancelStaking(it) }
                        )
                    }
                }
            }
        }
    }
}

@Composable
private fun StakingTypesList(
    types: List<StakingType>,
    onStake: (typeId: Int, durationId: Int, typeName: String, days: Int) -> Unit
) {
    LazyColumn(
        contentPadding = PaddingValues(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        items(types) { type ->
            var expanded by remember { mutableStateOf(false) }
            MysticCard(modifier = Modifier.fillMaxWidth()) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Column {
                            Text(
                                text = type.name ?: "",
                                color = MysticGold,
                                fontWeight = FontWeight.Bold,
                                style = MaterialTheme.typography.titleSmall
                            )
                            Text(
                                text = "${type.amountToken ?: 0} MYNU",
                                color = TextSecondary,
                                style = MaterialTheme.typography.bodySmall
                            )
                        }
                        Text(
                            text = "APR: ${type.apr ?: 0}%",
                            color = Color(0xFF22c55e),
                            fontWeight = FontWeight.Bold
                        )
                    }

                    Spacer(modifier = Modifier.height(8.dp))

                    TextButton(onClick = { expanded = !expanded }) {
                        Text(
                            text = if (expanded) "Sembunyikan Durasi" else "Lihat Durasi",
                            color = MysticPurpleLight
                        )
                    }

                    if (expanded) {
                        type.durations?.forEach { duration ->
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(vertical = 4.dp),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Column {
                                    Text(
                                        text = "${duration.days ?: 0} hari",
                                        color = TextSecondary,
                                        style = MaterialTheme.typography.bodyMedium
                                    )
                                    Text(
                                        text = "APR: ${duration.apr ?: type.apr ?: 0}%",
                                        color = TextSecondary,
                                        style = MaterialTheme.typography.labelSmall
                                    )
                                }
                                Button(
                                    onClick = {
                                        onStake(type.id, duration.id, type.name ?: "", duration.days ?: 0)
                                    },
                                    colors = ButtonDefaults.buttonColors(containerColor = MysticGold),
                                    contentPadding = PaddingValues(horizontal = 16.dp, vertical = 4.dp)
                                ) {
                                    Text("Stake", color = Color.Black, style = MaterialTheme.typography.labelSmall)
                                }
                            }
                            Divider(color = MysticPurple.copy(alpha = 0.2f))
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun UserStakingsList(
    stakings: List<UserStaking>,
    onClaim: (Int) -> Unit,
    onCancel: (Int) -> Unit
) {
    if (stakings.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text("Belum ada staking", color = TextSecondary)
        }
    } else {
        LazyColumn(
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            items(stakings) { staking ->
                val statusColor = when (staking.status) {
                    "active" -> Color(0xFF22c55e)
                    "claimed" -> Color(0xFF3b82f6)
                    "cancelled" -> Color(0xFFef4444)
                    else -> TextSecondary
                }

                MysticCard(modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = "${staking.amount ?: 0} MYNU",
                                color = MysticGold,
                                fontWeight = FontWeight.Bold,
                                style = MaterialTheme.typography.titleSmall
                            )
                            Surface(
                                shape = RoundedCornerShape(4.dp),
                                color = statusColor.copy(alpha = 0.2f)
                            ) {
                                Text(
                                    text = staking.status ?: "unknown",
                                    color = statusColor,
                                    style = MaterialTheme.typography.labelSmall,
                                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp)
                                )
                            }
                        }
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Reward: ${staking.expectedReward ?: 0} MYNU",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodySmall
                        )
                        Text(
                            text = "Mulai: ${staking.startDate?.take(10) ?: "-"}",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodySmall
                        )
                        Text(
                            text = "Berakhir: ${staking.endDate?.take(10) ?: "-"}",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodySmall
                        )

                        if (staking.status == "active") {
                            Spacer(modifier = Modifier.height(12.dp))
                            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                Button(
                                    onClick = { onClaim(staking.id) },
                                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF22c55e)),
                                    modifier = Modifier.weight(1f)
                                ) {
                                    Text("Claim", color = Color.White, style = MaterialTheme.typography.labelSmall)
                                }
                                OutlinedButton(
                                    onClick = { onCancel(staking.id) },
                                    modifier = Modifier.weight(1f),
                                    colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFFef4444))
                                ) {
                                    Text("Cancel", style = MaterialTheme.typography.labelSmall)
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
