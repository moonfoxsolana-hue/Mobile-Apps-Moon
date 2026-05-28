package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.local.TokenManager
import com.mysticnusa.app.data.repository.AuthRepository
import com.mysticnusa.app.navigation.Screen
import com.mysticnusa.app.ui.components.LoadingIndicator
import com.mysticnusa.app.ui.components.MysticButton
import com.mysticnusa.app.ui.components.MysticTextField
import com.mysticnusa.app.ui.theme.MysticGold
import com.mysticnusa.app.ui.theme.MysticPurple
import com.mysticnusa.app.ui.theme.MysticPurpleLight
import com.mysticnusa.app.ui.theme.TextSecondary
import com.mysticnusa.app.ui.viewmodels.AuthViewModel

@Composable
fun RegisterScreen(navController: NavController) {
    val context = LocalContext.current
    val tokenManager = remember { TokenManager(context.applicationContext) }
    val viewModel: AuthViewModel = viewModel(
        factory = AuthViewModel.Factory(AuthRepository(), tokenManager)
    )
    val uiState by viewModel.uiState.collectAsState()

    var name by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }
    var passwordVisible by remember { mutableStateOf(false) }

    LaunchedEffect(uiState.isLoggedIn) {
        if (uiState.isLoggedIn) {
            navController.navigate(Screen.Home.route) {
                popUpTo(Screen.Login.route) { inclusive = true }
            }
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(MaterialTheme.colorScheme.background)
    ) {
        if (uiState.isLoading) {
            LoadingIndicator()
        }

        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Text(
                text = "Mystic Nusa",
                style = MaterialTheme.typography.headlineLarge,
                color = MysticGold
            )

            Spacer(modifier = Modifier.height(8.dp))

            Text(
                text = "Buat akun baru",
                style = MaterialTheme.typography.bodyLarge,
                color = TextSecondary
            )

            Spacer(modifier = Modifier.height(32.dp))

            MysticTextField(
                value = name,
                onValueChange = { name = it },
                label = "Nama"
            )

            Spacer(modifier = Modifier.height(16.dp))

            MysticTextField(
                value = email,
                onValueChange = { email = it },
                label = "Email"
            )

            Spacer(modifier = Modifier.height(16.dp))

            OutlinedTextField(
                value = password,
                onValueChange = { password = it },
                label = { Text("Password") },
                modifier = Modifier.fillMaxWidth(),
                singleLine = true,
                visualTransformation = if (passwordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
                trailingIcon = {
                    IconButton(onClick = { passwordVisible = !passwordVisible }) {
                        Icon(
                            imageVector = if (passwordVisible) Icons.Default.Visibility else Icons.Default.VisibilityOff,
                            contentDescription = "Toggle password",
                            tint = TextSecondary
                        )
                    }
                },
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = MysticGold,
                    unfocusedBorderColor = MysticPurple,
                    focusedLabelColor = MysticGold,
                    unfocusedLabelColor = TextSecondary,
                    cursorColor = MysticGold
                )
            )

            Spacer(modifier = Modifier.height(16.dp))

            OutlinedTextField(
                value = confirmPassword,
                onValueChange = { confirmPassword = it },
                label = { Text("Konfirmasi Password") },
                modifier = Modifier.fillMaxWidth(),
                singleLine = true,
                visualTransformation = PasswordVisualTransformation(),
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = MysticGold,
                    unfocusedBorderColor = MysticPurple,
                    focusedLabelColor = MysticGold,
                    unfocusedLabelColor = TextSecondary,
                    cursorColor = MysticGold
                )
            )

            Spacer(modifier = Modifier.height(8.dp))

            uiState.error?.let { error ->
                Text(
                    text = error,
                    color = MaterialTheme.colorScheme.error,
                    style = MaterialTheme.typography.bodySmall,
                    modifier = Modifier.padding(vertical = 8.dp)
                )
            }

            Spacer(modifier = Modifier.height(16.dp))

            MysticButton(
                text = "Daftar",
                onClick = { viewModel.register(name, email, password, confirmPassword) },
                enabled = name.isNotBlank() && email.isNotBlank() &&
                        password.isNotBlank() && confirmPassword.isNotBlank() && !uiState.isLoading
            )

            Spacer(modifier = Modifier.height(24.dp))

            Row {
                Text(
                    text = "Sudah punya akun? ",
                    color = TextSecondary,
                    style = MaterialTheme.typography.bodyMedium
                )
                Text(
                    text = "Masuk",
                    color = MysticPurpleLight,
                    style = MaterialTheme.typography.bodyMedium,
                    modifier = Modifier.clickable {
                        navController.popBackStack()
                    }
                )
            }
        }
    }
}
