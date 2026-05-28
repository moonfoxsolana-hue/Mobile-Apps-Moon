package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.repository.AirdropRepository
import com.mysticnusa.app.ui.components.*
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.AirdropViewModel

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
        Column(
            modifier = Modifier
                .padding(paddingValues)
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(16.dp)
        ) {
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
                                viewModel.claimFirst()
                            }
                        },
                        enabled = walletAddress.isNotBlank() && !uiState.isLoading
                    )
                }
            }

            Spacer(modifier = Modifier.height(24.dp))

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

            // Status Messages
            if (uiState.isLoading) {
                Box(modifier = Modifier.fillMaxWidth(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(32.dp))
                }
            }

            uiState.claimResponse?.let { response ->
                MysticCard(modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Text(
                            text = if (response.status == "success") "Berhasil!" else "Info",
                            color = if (response.status == "success") Color(0xFF22c55e) else MysticGold,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = response.message ?: "",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodyMedium
                        )
                        response.tokensReceived?.let {
                            Text(
                                text = "+$it MYNU",
                                color = MysticGold,
                                fontWeight = FontWeight.Bold,
                                modifier = Modifier.padding(top = 4.dp)
                            )
                        }
                    }
                }
            }

            uiState.error?.let { error ->
                MysticCard(modifier = Modifier.fillMaxWidth()) {
                    Text(
                        text = error,
                        color = MaterialTheme.colorScheme.error,
                        modifier = Modifier.padding(16.dp)
                    )
                }
            }
        }
    }
}
