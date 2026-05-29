package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.background
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
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.models.StakingType
import com.mysticnusa.app.data.models.UserStaking
import com.mysticnusa.app.data.repository.StakingRepository
import com.mysticnusa.app.ui.components.ErrorMessage
import com.mysticnusa.app.ui.components.LoadingIndicator
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
    var selectedTab by remember { mutableStateOf(0) }
    var showStakeDialog by remember { mutableStateOf(false) }
    var selectedTypeId by remember { mutableStateOf(0) }
    var selectedDurationId by remember { mutableStateOf(0) }
    var selectedTypeName by remember { mutableStateOf("") }
    var selectedDurationDays by remember { mutableStateOf(0) }

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
            // Hero Section with gradient
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(
                        Brush.verticalGradient(
                            colors = listOf(MysticPurple, MysticDarkBackground)
                        )
                    )
                    .padding(24.dp),
                contentAlignment = Alignment.Center
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text(
                        text = "\uD83D\uDD12",
                        fontSize = 40.sp
                    )
                    Spacer(modifier = Modifier.height(12.dp))
                    Text(
                        text = "Stake MYNU Token",
                        color = MysticGold,
                        fontWeight = FontWeight.Bold,
                        fontSize = 20.sp,
                        textAlign = TextAlign.Center
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    Text(
                        text = "Dapatkan reward harian dengan mengunci token kamu",
                        color = TextSecondary,
                        style = MaterialTheme.typography.bodyMedium,
                        textAlign = TextAlign.Center
                    )
                }
            }

            // TabRow with custom styling
            TabRow(
                selectedTabIndex = selectedTab,
                containerColor = MysticSurface,
                contentColor = MysticGold,
                indicator = { tabPositions ->
                    TabRowDefaults.SecondaryIndicator(
                        modifier = Modifier.tabIndicatorOffset(tabPositions[selectedTab]),
                        height = 3.dp,
                        color = MysticGold
                    )
                }
            ) {
                Tab(
                    selected = selectedTab == 0,
                    onClick = { selectedTab = 0 },
                    text = {
                        Text(
                            "Paket Staking",
                            color = if (selectedTab == 0) MysticGold else TextSecondary,
                            fontWeight = if (selectedTab == 0) FontWeight.Bold else FontWeight.Normal
                        )
                    }
                )
                Tab(
                    selected = selectedTab == 1,
                    onClick = { selectedTab = 1 },
                    text = {
                        Text(
                            "Staking Saya",
                            color = if (selectedTab == 1) MysticGold else TextSecondary,
                            fontWeight = if (selectedTab == 1) FontWeight.Bold else FontWeight.Normal
                        )
                    }
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
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        items(types) { type ->
            var expanded by remember { mutableStateOf(false) }
            Card(
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(16.dp),
                colors = CardDefaults.cardColors(containerColor = MysticSurface),
                elevation = CardDefaults.cardElevation(defaultElevation = 4.dp)
            ) {
                Column {
                    // Gradient accent at the top
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(4.dp)
                            .background(
                                Brush.horizontalGradient(
                                    colors = listOf(MysticPurple, MysticGold)
                                )
                            )
                    )
                    Column(modifier = Modifier.padding(16.dp)) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                // Staking type icon
                                Surface(
                                    shape = RoundedCornerShape(12.dp),
                                    color = MysticPurple.copy(alpha = 0.2f),
                                    modifier = Modifier.size(44.dp)
                                ) {
                                    Box(contentAlignment = Alignment.Center) {
                                        Text("\uD83D\uDCB0", fontSize = 22.sp)
                                    }
                                }
                                Spacer(modifier = Modifier.width(12.dp))
                                Column {
                                    Text(
                                        text = type.name ?: "",
                                        color = MysticGold,
                                        fontWeight = FontWeight.Bold,
                                        style = MaterialTheme.typography.titleMedium
                                    )
                                    Text(
                                        text = "${type.amountToken ?: 0} MYNU",
                                        color = TextSecondary,
                                        style = MaterialTheme.typography.bodySmall
                                    )
                                }
                            }
                            // APR Badge
                            Surface(
                                shape = RoundedCornerShape(20.dp),
                                color = Color(0xFF22c55e).copy(alpha = 0.15f)
                            ) {
                                Text(
                                    text = "${type.apr ?: 0}% APR",
                                    color = Color(0xFF22c55e),
                                    fontWeight = FontWeight.Bold,
                                    fontSize = 14.sp,
                                    modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp)
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(12.dp))

                        TextButton(onClick = { expanded = !expanded }) {
                            Text(
                                text = if (expanded) "\u25B2 Sembunyikan Durasi" else "\u25BC Lihat Durasi",
                                color = MysticPurpleLight
                            )
                        }

                        if (expanded) {
                            Spacer(modifier = Modifier.height(8.dp))
                            type.durations?.forEach { duration ->
                                Surface(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .padding(vertical = 4.dp),
                                    shape = RoundedCornerShape(10.dp),
                                    color = MysticDarkBackground.copy(alpha = 0.5f)
                                ) {
                                    Row(
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .padding(12.dp),
                                        horizontalArrangement = Arrangement.SpaceBetween,
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Column {
                                            Text(
                                                text = "${duration.days ?: 0} hari",
                                                color = Color.White,
                                                fontWeight = FontWeight.Medium,
                                                style = MaterialTheme.typography.bodyMedium
                                            )
                                            Text(
                                                text = "APR: ${duration.apr ?: type.apr ?: 0}%",
                                                color = Color(0xFF22c55e),
                                                style = MaterialTheme.typography.labelSmall
                                            )
                                        }
                                        Button(
                                            onClick = {
                                                onStake(type.id, duration.id, type.name ?: "", duration.days ?: 0)
                                            },
                                            colors = ButtonDefaults.buttonColors(containerColor = MysticGold),
                                            shape = RoundedCornerShape(20.dp),
                                            contentPadding = PaddingValues(horizontal = 20.dp, vertical = 6.dp)
                                        ) {
                                            Text("Stake", color = Color.Black, fontWeight = FontWeight.Bold, style = MaterialTheme.typography.labelSmall)
                                        }
                                    }
                                }
                            }
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
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                Text("\uD83D\uDCED", fontSize = 40.sp)
                Spacer(modifier = Modifier.height(12.dp))
                Text("Belum ada staking", color = TextSecondary, style = MaterialTheme.typography.bodyLarge)
            }
        }
    } else {
        LazyColumn(
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            items(stakings) { staking ->
                val statusColor = when (staking.status) {
                    "active" -> Color(0xFF22c55e)
                    "claimed" -> Color(0xFF3b82f6)
                    "cancelled" -> Color(0xFFef4444)
                    else -> TextSecondary
                }
                val statusIcon = when (staking.status) {
                    "active" -> "\u23F3"
                    "claimed" -> "\u2705"
                    "cancelled" -> "\u274C"
                    else -> "\u2753"
                }

                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(16.dp),
                    colors = CardDefaults.cardColors(containerColor = MysticSurface),
                    elevation = CardDefaults.cardElevation(defaultElevation = 4.dp)
                ) {
                    Column {
                        // Gradient accent at the top
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(4.dp)
                                .background(
                                    Brush.horizontalGradient(
                                        colors = listOf(statusColor, statusColor.copy(alpha = 0.3f))
                                    )
                                )
                        )
                        Column(modifier = Modifier.padding(16.dp)) {
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Text(statusIcon, fontSize = 20.sp)
                                    Spacer(modifier = Modifier.width(8.dp))
                                    Text(
                                        text = "${staking.amount ?: 0} MYNU",
                                        color = MysticGold,
                                        fontWeight = FontWeight.Bold,
                                        style = MaterialTheme.typography.titleMedium
                                    )
                                }
                                // Status pill badge
                                Surface(
                                    shape = RoundedCornerShape(20.dp),
                                    color = statusColor.copy(alpha = 0.15f)
                                ) {
                                    Text(
                                        text = staking.status ?: "unknown",
                                        color = statusColor,
                                        fontWeight = FontWeight.Medium,
                                        style = MaterialTheme.typography.labelSmall,
                                        modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp)
                                    )
                                }
                            }

                            Spacer(modifier = Modifier.height(12.dp))

                            // Reward info
                            Surface(
                                shape = RoundedCornerShape(8.dp),
                                color = MysticGold.copy(alpha = 0.1f),
                                modifier = Modifier.fillMaxWidth()
                            ) {
                                Row(
                                    modifier = Modifier.padding(10.dp),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Text("\uD83C\uDFC6", fontSize = 16.sp)
                                    Spacer(modifier = Modifier.width(8.dp))
                                    Text(
                                        text = "Reward: ${staking.expectedReward ?: 0} MYNU",
                                        color = MysticGold,
                                        fontWeight = FontWeight.Medium,
                                        style = MaterialTheme.typography.bodySmall
                                    )
                                }
                            }

                            Spacer(modifier = Modifier.height(12.dp))

                            // Progress bar for active staking
                            if (staking.status == "active") {
                                val progress = calculateStakingProgress(staking.startDate, staking.endDate)
                                Column {
                                    Row(
                                        modifier = Modifier.fillMaxWidth(),
                                        horizontalArrangement = Arrangement.SpaceBetween
                                    ) {
                                        Text(
                                            text = staking.startDate?.take(10) ?: "-",
                                            color = TextSecondary,
                                            style = MaterialTheme.typography.labelSmall
                                        )
                                        Text(
                                            text = staking.endDate?.take(10) ?: "-",
                                            color = TextSecondary,
                                            style = MaterialTheme.typography.labelSmall
                                        )
                                    }
                                    Spacer(modifier = Modifier.height(4.dp))
                                    LinearProgressIndicator(
                                        progress = { progress },
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .height(6.dp)
                                            .clip(RoundedCornerShape(3.dp)),
                                        color = MysticGold,
                                        trackColor = MysticPurple.copy(alpha = 0.3f)
                                    )
                                }
                            } else {
                                Row(
                                    modifier = Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween
                                ) {
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
                                }
                            }

                            if (staking.status == "active") {
                                Spacer(modifier = Modifier.height(16.dp))
                                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                    Button(
                                        onClick = { onClaim(staking.id) },
                                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF22c55e)),
                                        shape = RoundedCornerShape(20.dp),
                                        modifier = Modifier.weight(1f)
                                    ) {
                                        Text("Claim", color = Color.White, fontWeight = FontWeight.Bold, style = MaterialTheme.typography.labelSmall)
                                    }
                                    OutlinedButton(
                                        onClick = { onCancel(staking.id) },
                                        modifier = Modifier.weight(1f),
                                        shape = RoundedCornerShape(20.dp),
                                        colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFFef4444))
                                    ) {
                                        Text("Cancel", fontWeight = FontWeight.Bold, style = MaterialTheme.typography.labelSmall)
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

private fun calculateStakingProgress(startDate: String?, endDate: String?): Float {
    if (startDate == null || endDate == null) return 0f
    try {
        val start = java.time.LocalDate.parse(startDate.take(10))
        val end = java.time.LocalDate.parse(endDate.take(10))
        val now = java.time.LocalDate.now()
        val totalDays = java.time.temporal.ChronoUnit.DAYS.between(start, end)
        if (totalDays <= 0) return 0f
        val elapsedDays = java.time.temporal.ChronoUnit.DAYS.between(start, now)
        val progress = elapsedDays.toFloat() / totalDays.toFloat()
        return progress.coerceIn(0f, 1f)
    } catch (e: Exception) {
        return 0f
    }
}
