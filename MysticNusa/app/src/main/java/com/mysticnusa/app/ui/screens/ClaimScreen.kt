package com.mysticnusa.app.ui.screens

import androidx.compose.animation.*
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
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
import com.mysticnusa.app.data.repository.AirdropRepository
import com.mysticnusa.app.ui.components.*
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.AirdropViewModel

private val SuccessColor = Color(0xFF22c55e)
private val ErrorColor = Color(0xFFef4444)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ClaimScreen(navController: NavController) {
    val viewModel: AirdropViewModel = viewModel(
        factory = AirdropViewModel.Factory(AirdropRepository())
    )
    val uiState by viewModel.uiState.collectAsState()

    var walletAddress by remember { mutableStateOf("") }
    var code by remember { mutableStateOf("") }
    var walletError by remember { mutableStateOf<String?>(null) }

    val solanaAddressRegex = Regex("^[1-9A-HJ-NP-Za-km-z]{32,44}$")

    // Auto-dismiss notification after 3 seconds
    LaunchedEffect(uiState.notificationCounter) {
        if (uiState.claimResponse != null || uiState.error != null) {
            kotlinx.coroutines.delay(3000)
            viewModel.clearMessage()
            viewModel.clearError()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Klaim Airdrop", color = MysticGold) },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.background
                )
            )
        },
        bottomBar = { BottomNavBar(navController) },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        Box(
            modifier = Modifier
                .padding(paddingValues)
                .fillMaxSize()
        ) {
            // Main scrollable content
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .padding(16.dp)
            ) {
                // Header Section
                Column(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Text(
                        text = "\u2728",
                        fontSize = 40.sp
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    Text(
                        text = "Program Airdrop MYNU",
                        color = MysticGold,
                        fontWeight = FontWeight.Bold,
                        fontSize = 20.sp
                    )
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = "Klaim token gratis dan bergabung dengan komunitas Mystic Nusa",
                        color = TextSecondary,
                        style = MaterialTheme.typography.bodySmall,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.padding(horizontal = 24.dp)
                    )
                }

                Spacer(modifier = Modifier.height(24.dp))

                // First Claim Section
                MysticCard(modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Text(
                            text = "Klaim Airdrop Pertama",
                            color = MysticGold,
                            fontWeight = FontWeight.Bold,
                            style = MaterialTheme.typography.titleMedium
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Masukkan alamat wallet Solana untuk klaim 1000 token MYNU pertamamu.",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodySmall
                        )
                        Spacer(modifier = Modifier.height(16.dp))

                        MysticTextField(
                            value = walletAddress,
                            onValueChange = {
                                walletAddress = it
                                walletError = null
                            },
                            label = "Alamat Wallet Solana"
                        )

                        walletError?.let {
                            Text(
                                text = it,
                                color = MaterialTheme.colorScheme.error,
                                style = MaterialTheme.typography.bodySmall,
                                modifier = Modifier.padding(top = 4.dp)
                            )
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        MysticButton(
                            text = "Klaim 1000 Token",
                            onClick = {
                                if (!solanaAddressRegex.matches(walletAddress)) {
                                    walletError = "Alamat wallet Solana tidak valid"
                                } else {
                                    viewModel.claimFirst(walletAddress)
                                }
                            },
                            enabled = walletAddress.isNotBlank() && !uiState.isLoading && !uiState.hasClaimed
                        )
                    }
                }

                Spacer(modifier = Modifier.height(20.dp))

                // Daily Claim Section
                MysticCard(modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Text(
                            text = "Klaim Harian dengan Kode",
                            color = MysticGold,
                            fontWeight = FontWeight.Bold,
                            style = MaterialTheme.typography.titleMedium
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Masukkan kode airdrop dari komunitas untuk klaim token harian.",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodySmall
                        )
                        Spacer(modifier = Modifier.height(16.dp))

                        MysticTextField(
                            value = code,
                            onValueChange = { code = it },
                            label = "Kode Airdrop"
                        )

                        Spacer(modifier = Modifier.height(16.dp))

                        MysticButton(
                            text = "Klaim",
                            onClick = { viewModel.claimWithCode(code) },
                            enabled = code.isNotBlank() && !uiState.isLoading
                        )
                    }
                }

                Spacer(modifier = Modifier.height(16.dp))

                // Loading indicator
                if (uiState.isLoading) {
                    Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                        CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
                    }
                }
            }

            // Floating top notification overlay
            AnimatedVisibility(
                visible = uiState.claimResponse != null || uiState.error != null,
                enter = slideInVertically(initialOffsetY = { -it }) + fadeIn(),
                exit = slideOutVertically(targetOffsetY = { -it }) + fadeOut(),
                modifier = Modifier.align(Alignment.TopCenter)
            ) {
                val isError = uiState.error != null
                val text = if (isError) {
                    uiState.error ?: ""
                } else {
                    val response = uiState.claimResponse
                    val msg = response?.message ?: ""
                    val amount = response?.amount
                    if (amount != null) "$msg (+$amount MYNU)" else msg
                }
                Card(
                    modifier = Modifier
                        .padding(horizontal = 16.dp, vertical = 8.dp)
                        .fillMaxWidth(),
                    shape = RoundedCornerShape(12.dp),
                    colors = CardDefaults.cardColors(
                        containerColor = if (isError) Color(0xFF4a1010) else Color(0xFF1a3a1a)
                    ),
                    border = BorderStroke(
                        1.dp,
                        if (isError) ErrorColor.copy(alpha = 0.5f) else SuccessColor.copy(alpha = 0.5f)
                    )
                ) {
                    Row(
                        modifier = Modifier.padding(horizontal = 16.dp, vertical = 12.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(
                            text = if (isError) "!" else "\u2713",
                            color = if (isError) ErrorColor else SuccessColor,
                            fontWeight = FontWeight.Bold,
                            fontSize = 16.sp,
                            modifier = Modifier
                                .size(24.dp)
                                .background(
                                    if (isError) ErrorColor.copy(alpha = 0.2f) else SuccessColor.copy(alpha = 0.2f),
                                    CircleShape
                                )
                                .wrapContentSize(Alignment.Center)
                        )
                        Spacer(modifier = Modifier.width(12.dp))
                        Text(
                            text = text,
                            color = Color.White,
                            fontSize = 14.sp,
                            modifier = Modifier.weight(1f)
                        )
                    }
                }
            }
        }
    }
}
