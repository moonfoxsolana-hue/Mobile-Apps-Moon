package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.navigation.NavController
import com.mysticnusa.app.data.local.TokenManager
import com.mysticnusa.app.navigation.Screen
import com.mysticnusa.app.ui.theme.MysticGold
import com.mysticnusa.app.ui.theme.TextSecondary
import kotlinx.coroutines.delay

@Composable
fun SplashScreen(navController: NavController, tokenManager: TokenManager) {
    LaunchedEffect(Unit) {
        delay(1500)
        val token = tokenManager.getToken()
        val destination = if (!token.isNullOrEmpty()) Screen.Home.route else Screen.Login.route
        navController.navigate(destination) {
            popUpTo(Screen.Splash.route) { inclusive = true }
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(MaterialTheme.colorScheme.background),
        contentAlignment = Alignment.Center
    ) {
        Column(
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(
                text = "Mystic Nusa",
                fontSize = 36.sp,
                fontWeight = FontWeight.Bold,
                color = MysticGold
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                text = "\$MYNU",
                fontSize = 18.sp,
                color = MysticGold.copy(alpha = 0.7f)
            )
            Spacer(modifier = Modifier.height(16.dp))
            Text(
                text = "Legenda Desa Mistis",
                fontSize = 14.sp,
                color = TextSecondary
            )
        }
    }
}
