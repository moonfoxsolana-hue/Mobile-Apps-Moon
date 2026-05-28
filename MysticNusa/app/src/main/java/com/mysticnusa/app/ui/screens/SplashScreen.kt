package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.navigation.NavController
import com.mysticnusa.app.data.local.TokenManager
import com.mysticnusa.app.navigation.Screen
import com.mysticnusa.app.ui.theme.MysticGold
import kotlinx.coroutines.delay

@Composable
fun SplashScreen(
    navController: NavController,
    tokenManager: TokenManager
) {
    LaunchedEffect(Unit) {
        delay(2000)
        val token = tokenManager.getToken()
        val destination = if (token != null) Screen.Home.route else Screen.Login.route
        navController.navigate(destination) {
            popUpTo(Screen.Splash.route) { inclusive = true }
        }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(MaterialTheme.colorScheme.background),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Text(
            text = "MysticNusa",
            style = MaterialTheme.typography.headlineLarge,
            color = MysticGold
        )
    }
}
