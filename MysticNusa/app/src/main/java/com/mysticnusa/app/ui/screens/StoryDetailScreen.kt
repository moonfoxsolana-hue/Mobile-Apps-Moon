package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import com.mysticnusa.app.data.repository.StoryRepository
import com.mysticnusa.app.ui.components.ErrorMessage
import com.mysticnusa.app.ui.components.LoadingIndicator
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.StoryViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StoryDetailScreen(navController: NavController, storyId: Int) {
    val viewModel: StoryViewModel = viewModel(
        factory = StoryViewModel.Factory(StoryRepository())
    )
    val uiState by viewModel.uiState.collectAsState()

    LaunchedEffect(Unit) {
        if (uiState.stories.isEmpty()) {
            viewModel.loadStories()
        }
    }

    val storyItem = uiState.stories.find { it.id == storyId }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Detail Cerita", color = MysticGold) },
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
                storyItem == null -> ErrorMessage(
                    message = "Cerita tidak ditemukan",
                    onRetry = { viewModel.loadStories() }
                )
                else -> {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                            .padding(16.dp)
                    ) {
                        Text(
                            text = storyItem.title ?: "",
                            color = MysticGold,
                            fontWeight = FontWeight.Bold,
                            style = MaterialTheme.typography.titleLarge
                        )
                        Spacer(modifier = Modifier.height(8.dp))

                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            storyItem.theme?.let { theme ->
                                Surface(
                                    shape = RoundedCornerShape(4.dp),
                                    color = MysticPurple.copy(alpha = 0.2f)
                                ) {
                                    Text(
                                        text = theme,
                                        color = MysticPurpleLight,
                                        style = MaterialTheme.typography.labelSmall,
                                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
                                    )
                                }
                            }
                            Text(
                                text = storyItem.date?.take(10) ?: "",
                                color = TextSecondary,
                                style = MaterialTheme.typography.labelSmall
                            )
                        }

                        storyItem.audioPath?.let {
                            Spacer(modifier = Modifier.height(16.dp))
                            Button(
                                onClick = { /* Audio playback placeholder */ },
                                colors = ButtonDefaults.buttonColors(
                                    containerColor = MysticPurple
                                )
                            ) {
                                Icon(Icons.Default.PlayArrow, "Play", tint = MysticGold)
                                Spacer(modifier = Modifier.width(8.dp))
                                Text("Dengarkan Audio", color = MysticGold)
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = storyItem.content ?: "",
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodyMedium
                        )
                    }
                }
            }
        }
    }
}
