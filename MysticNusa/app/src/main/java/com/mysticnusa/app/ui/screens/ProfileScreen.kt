package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Person
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.remote.RetrofitInstance
import com.mysticnusa.app.data.repository.AuthRepository
import com.mysticnusa.app.data.repository.ProfileRepository
import com.mysticnusa.app.navigation.Screen
import com.mysticnusa.app.ui.components.*
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.AuthViewModel
import com.mysticnusa.app.ui.viewmodels.ProfileViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProfileScreen(navController: NavController) {
    val tokenManager = remember { RetrofitInstance.tokenManager!! }
    val profileViewModel: ProfileViewModel = viewModel(
        factory = ProfileViewModel.Factory(ProfileRepository())
    )
    val authViewModel: AuthViewModel = viewModel(
        factory = AuthViewModel.Factory(AuthRepository(), tokenManager)
    )
    val profileState by profileViewModel.uiState.collectAsState()
    val authState by authViewModel.uiState.collectAsState()

    LaunchedEffect(Unit) {
        profileViewModel.loadProfile()
    }

    LaunchedEffect(authState.isLoggedIn, authState.authResponse) {
        if (!authState.isLoggedIn && authState.authResponse == null && authState.error == null && !authState.isLoading) {
            // After logout
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Profil", color = MysticGold) },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.background
                )
            )
        },
        bottomBar = { BottomNavBar(navController) },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        Box(modifier = Modifier.padding(paddingValues).fillMaxSize()) {
            when {
                profileState.isLoading -> LoadingIndicator()
                profileState.error != null -> ErrorMessage(
                    message = profileState.error ?: "Terjadi kesalahan",
                    onRetry = { profileViewModel.loadProfile() }
                )
                else -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(16.dp)
                    ) {
                        MysticCard(modifier = Modifier.fillMaxWidth()) {
                            Column(
                                modifier = Modifier.padding(20.dp),
                                horizontalAlignment = Alignment.CenterHorizontally
                            ) {
                                Icon(
                                    imageVector = Icons.Default.Person,
                                    contentDescription = "Profile",
                                    tint = MysticGold,
                                    modifier = Modifier.size(64.dp)
                                )
                                Spacer(modifier = Modifier.height(12.dp))

                                profileState.profile?.let { profile ->
                                    Text(
                                        text = profile.name ?: "User",
                                        fontSize = 20.sp,
                                        fontWeight = FontWeight.Bold,
                                        color = Color.White
                                    )
                                    Spacer(modifier = Modifier.height(4.dp))
                                    Text(
                                        text = profile.email ?: "",
                                        color = TextSecondary,
                                        style = MaterialTheme.typography.bodyMedium
                                    )
                                    Spacer(modifier = Modifier.height(16.dp))

                                    Divider(color = MysticPurple.copy(alpha = 0.3f))
                                    Spacer(modifier = Modifier.height(16.dp))

                                    ProfileRow("Wallet", profile.walletAddress ?: "Belum diatur")
                                    Spacer(modifier = Modifier.height(8.dp))
                                    ProfileRow("Total Token", "${profile.totalToken ?: 0.0} MYNU", valueColor = MysticGold)
                                    Spacer(modifier = Modifier.height(8.dp))
                                    ProfileRow("Locked Balance", "${profile.lockedBalance ?: 0.0} MYNU")
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        MysticButton(
                            text = "Riwayat Token",
                            onClick = { navController.navigate(Screen.TokenHistory.route) }
                        )

                        Spacer(modifier = Modifier.height(32.dp))

                        Button(
                            onClick = {
                                authViewModel.logout()
                                navController.navigate(Screen.Login.route) {
                                    popUpTo(0) { inclusive = true }
                                }
                            },
                            modifier = Modifier.fillMaxWidth().height(50.dp),
                            colors = ButtonDefaults.buttonColors(
                                containerColor = Color(0xFFdc2626)
                            ),
                            shape = MaterialTheme.shapes.medium
                        ) {
                            Text("Logout", color = Color.White)
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun ProfileRow(label: String, value: String, valueColor: Color = TextSecondary) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween
    ) {
        Text(text = label, color = TextSecondary, style = MaterialTheme.typography.bodyMedium)
        Text(
            text = value,
            color = valueColor,
            style = MaterialTheme.typography.bodyMedium,
            fontWeight = FontWeight.Medium
        )
    }
}
