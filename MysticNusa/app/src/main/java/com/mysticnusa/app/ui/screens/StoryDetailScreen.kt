package com.mysticnusa.app.ui.screens

import android.media.MediaPlayer
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.Pause
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import coil.compose.AsyncImage
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
                    val mediaPlayer = remember { MediaPlayer() }
                    var isPlaying by remember { mutableStateOf(false) }

                    DisposableEffect(Unit) {
                        onDispose {
                            mediaPlayer.release()
                        }
                    }

                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .verticalScroll(rememberScrollState())
                    ) {
                        storyItem.imagePath?.let {
                            AsyncImage(
                                model = "https://mystical-nusa.web.id/${storyItem.imagePath?.trimStart('/')}",
                                contentDescription = storyItem.title,
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .wrapContentHeight()
                                    .clip(RoundedCornerShape(bottomStart = 16.dp, bottomEnd = 16.dp)),
                                contentScale = ContentScale.FillWidth
                            )
                        }

                        Column(modifier = Modifier.padding(16.dp)) {
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
                                    onClick = {
                                        if (!isPlaying) {
                                            try {
                                                mediaPlayer.reset()
                                                mediaPlayer.setDataSource("https://mystical-nusa.web.id/cerita/audio/${storyItem.id}")
                                                mediaPlayer.setOnPreparedListener { mp ->
                                                    mp.start()
                                                    isPlaying = true
                                                }
                                                mediaPlayer.setOnCompletionListener {
                                                    isPlaying = false
                                                }
                                                mediaPlayer.setOnErrorListener { _, _, _ ->
                                                    isPlaying = false
                                                    true
                                                }
                                                mediaPlayer.prepareAsync()
                                            } catch (e: Exception) {
                                                isPlaying = false
                                            }
                                        } else {
                                            try {
                                                mediaPlayer.stop()
                                            } catch (_: IllegalStateException) {
                                                // stop() called during prepareAsync - safe to ignore
                                            }
                                            mediaPlayer.reset()
                                            isPlaying = false
                                        }
                                    },
                                    colors = ButtonDefaults.buttonColors(
                                        containerColor = MysticPurple
                                    )
                                ) {
                                    Icon(
                                        if (isPlaying) Icons.Default.Pause else Icons.Default.PlayArrow,
                                        contentDescription = if (isPlaying) "Pause" else "Play",
                                        tint = MysticGold
                                    )
                                    Spacer(modifier = Modifier.width(8.dp))
                                    Text(
                                        if (isPlaying) "Berhenti" else "Dengarkan Audio",
                                        color = MysticGold
                                    )
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
}
