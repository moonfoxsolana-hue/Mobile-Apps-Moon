package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.lazy.rememberLazyListState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavController
import coil.compose.AsyncImage
import com.mysticnusa.app.data.models.StoryItem
import com.mysticnusa.app.data.repository.StoryRepository
import com.mysticnusa.app.navigation.Screen
import com.mysticnusa.app.ui.components.ErrorMessage
import com.mysticnusa.app.ui.components.LoadingIndicator
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.theme.*
import com.mysticnusa.app.ui.viewmodels.StoryViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StoriesScreen(navController: NavController) {
    val viewModel: StoryViewModel = viewModel(
        factory = StoryViewModel.Factory(StoryRepository())
    )
    val uiState by viewModel.uiState.collectAsState()
    val listState = rememberLazyListState()

    LaunchedEffect(Unit) {
        if (uiState.stories.isEmpty()) {
            viewModel.loadStories()
        }
    }

    val shouldLoadMore = remember {
        derivedStateOf {
            val lastVisibleItem = listState.layoutInfo.visibleItemsInfo.lastOrNull()?.index ?: 0
            lastVisibleItem >= uiState.stories.size - 3
        }
    }

    LaunchedEffect(shouldLoadMore.value) {
        if (shouldLoadMore.value && !uiState.isLoading) {
            viewModel.loadMore()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Cerita Mistis", color = MysticGold) },
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
                uiState.isLoading && uiState.stories.isEmpty() -> LoadingIndicator()
                uiState.error != null && uiState.stories.isEmpty() -> ErrorMessage(
                    message = uiState.error ?: "Terjadi kesalahan",
                    onRetry = { viewModel.loadStories() }
                )
                else -> {
                    LazyColumn(
                        state = listState,
                        modifier = Modifier.fillMaxSize(),
                        contentPadding = PaddingValues(16.dp),
                        verticalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        items(uiState.stories) { item ->
                            StoryCard(item = item, onClick = {
                                navController.navigate(Screen.StoryDetail.createRoute(item.id))
                            })
                        }
                        if (uiState.isLoading && uiState.stories.isNotEmpty()) {
                            item {
                                Box(
                                    modifier = Modifier.fillMaxWidth().padding(16.dp),
                                    contentAlignment = Alignment.Center
                                ) {
                                    CircularProgressIndicator(color = MysticGold, modifier = Modifier.size(24.dp))
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
private fun StoryCard(item: StoryItem, onClick: () -> Unit) {
    MysticCard(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() }
    ) {
        Row(modifier = Modifier.padding(16.dp)) {
            item.imagePath?.let { imageUrl ->
                AsyncImage(
                    model = imageUrl,
                    contentDescription = item.title,
                    modifier = Modifier
                        .size(80.dp)
                        .clip(RoundedCornerShape(8.dp)),
                    contentScale = ContentScale.Crop
                )
                Spacer(modifier = Modifier.width(12.dp))
            }
            Column(modifier = Modifier.weight(1f)) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        text = item.date?.take(10) ?: "",
                        color = MysticPurpleLight,
                        style = MaterialTheme.typography.labelSmall
                    )
                    item.theme?.let { theme ->
                        Surface(
                            shape = RoundedCornerShape(4.dp),
                            color = MysticPurple.copy(alpha = 0.2f)
                        ) {
                            Text(
                                text = theme,
                                color = MysticPurpleLight,
                                style = MaterialTheme.typography.labelSmall,
                                modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp)
                            )
                        }
                    }
                }
                Spacer(modifier = Modifier.height(8.dp))
                Text(
                    text = item.title ?: "",
                    color = MysticGold,
                    fontWeight = FontWeight.Bold,
                    style = MaterialTheme.typography.titleSmall,
                    maxLines = 2,
                    overflow = TextOverflow.Ellipsis
                )
            }
        }
    }
}
