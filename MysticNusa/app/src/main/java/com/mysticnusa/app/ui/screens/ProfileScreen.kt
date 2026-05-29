package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Star
import androidx.compose.material.icons.automirrored.filled.ExitToApp
import androidx.compose.material.icons.automirrored.filled.List
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
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
    val tokenManager = RetrofitInstance.tokenManager
    if (tokenManager == null) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = MysticGold)
        }
        return
    }

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
                            .padding(16.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        // Avatar Section
                        Box(
                            modifier = Modifier
                                .size(120.dp)
                                .clip(CircleShape)
                                .background(
                                    Brush.radialGradient(
                                        colors = listOf(
                                            MysticPurple.copy(alpha = 0.8f),
                                            MysticGold.copy(alpha = 0.4f),
                                            MysticPurple.copy(alpha = 0.2f)
                                        )
                                    )
                                ),
                            contentAlignment = Alignment.Center
                        ) {
                            Icon(
                                imageVector = Icons.Default.Person,
                                contentDescription = "Profile",
                                tint = MysticGold,
                                modifier = Modifier.size(96.dp)
                            )
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        profileState.profile?.let { profile ->
                            Text(
                                text = profile.name ?: "User",
                                fontSize = 22.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color.White
                            )
                            Spacer(modifier = Modifier.height(4.dp))
                            Text(
                                text = profile.email ?: "",
                                color = TextSecondary,
                                style = MaterialTheme.typography.bodyMedium
                            )
                        }

                        Spacer(modifier = Modifier.height(20.dp))

                        // Decorative divider
                        HorizontalDivider(
                            color = MysticPurple.copy(alpha = 0.4f),
                            thickness = 1.dp,
                            modifier = Modifier.padding(horizontal = 32.dp)
                        )

                        Spacer(modifier = Modifier.height(20.dp))

                        // Stats Header
                        Text(
                            text = "Stats",
                            color = MysticGold,
                            fontWeight = FontWeight.Bold,
                            fontSize = 16.sp,
                            modifier = Modifier.align(Alignment.Start)
                        )

                        Spacer(modifier = Modifier.height(12.dp))

                        profileState.profile?.let { profile ->
                            // Wallet Row
                            ProfileInfoCard(
                                icon = Icons.AutoMirrored.Filled.List,
                                label = "Wallet",
                                value = profile.walletAddress ?: "Belum diatur"
                            )

                            Spacer(modifier = Modifier.height(10.dp))

                            // Total Token Row
                            ProfileInfoCard(
                                icon = Icons.Default.Star,
                                label = "Total Token",
                                value = "${profile.totalToken ?: "0"} MYNU",
                                valueColor = MysticGold
                            )

                            Spacer(modifier = Modifier.height(10.dp))

                            // Locked Balance Row
                            ProfileInfoCard(
                                icon = Icons.Default.Lock,
                                label = "Locked Balance",
                                value = "${profile.lockedBalance ?: "0"} MYNU"
                            )
                        }

                        Spacer(modifier = Modifier.height(20.dp))

                        MysticButton(
                            text = "Riwayat Token",
                            onClick = { navController.navigate(Screen.TokenHistory.route) }
                        )

                        Spacer(modifier = Modifier.height(48.dp))

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
                            Icon(
                                imageVector = Icons.AutoMirrored.Filled.ExitToApp,
                                contentDescription = null,
                                tint = Color.White,
                                modifier = Modifier.size(20.dp)
                            )
                            Spacer(modifier = Modifier.width(8.dp))
                            Text("Logout", color = Color.White)
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun ProfileInfoCard(
    icon: ImageVector,
    label: String,
    value: String,
    valueColor: Color = TextSecondary
) {
    Surface(
        modifier = Modifier.fillMaxWidth(),
        shape = MaterialTheme.shapes.medium,
        color = MysticSurface,
        tonalElevation = 2.dp
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 14.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(
                imageVector = icon,
                contentDescription = null,
                tint = MysticPurpleLight,
                modifier = Modifier.size(20.dp)
            )
            Spacer(modifier = Modifier.width(12.dp))
            Text(
                text = label,
                color = TextSecondary,
                style = MaterialTheme.typography.bodyMedium
            )
            Spacer(modifier = Modifier.weight(1f))
            Text(
                text = value,
                color = valueColor,
                style = MaterialTheme.typography.bodyMedium,
                fontWeight = FontWeight.Medium
            )
        }
    }
}
