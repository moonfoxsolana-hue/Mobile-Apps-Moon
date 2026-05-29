package com.mysticnusa.app.ui.screens

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.navigation.NavController
import coil.compose.AsyncImage
import com.mysticnusa.app.navigation.Screen
import com.mysticnusa.app.ui.components.BottomNavBar
import com.mysticnusa.app.ui.components.MysticCard
import com.mysticnusa.app.ui.theme.*

private data class GameItem(
    val title: String,
    val description: String,
    val imageUrl: String,
    val route: String
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GamesScreen(navController: NavController) {
    val games = listOf(
        GameItem(
            title = "Ngepet Online",
            description = "Curi token dari rumah!",
            imageUrl = "https://mystical-nusa.web.id/images/ngepet-online.jpg",
            route = Screen.NgepetGame.route
        ),
        GameItem(
            title = "Intuition",
            description = "Ikuti intuisimu!",
            imageUrl = "https://mystical-nusa.web.id/images/intuition-test.jpg",
            route = Screen.IntuitionGame.route
        ),
        GameItem(
            title = "Mystical Logic of Minds",
            description = "Tes kecerdasan logikamu!",
            imageUrl = "https://mystical-nusa.web.id/images/logic-minds.jpg",
            route = Screen.LogicalGame.route
        ),
        GameItem(
            title = "Arcane of Trivia",
            description = "Uji pengetahuanmu!",
            imageUrl = "https://mystical-nusa.web.id/images/trivia.jpg",
            route = Screen.TriviaGame.route
        ),
        GameItem(
            title = "Tarot of Mystic Nusa",
            description = "Baca takdirmu hari ini",
            imageUrl = "https://mystical-nusa.web.id/images/tarot.jpg",
            route = Screen.TarotGame.route
        ),
        GameItem(
            title = "Ular Tangga Mystic",
            description = "Board game klasik multiplayer",
            imageUrl = "https://mystical-nusa.web.id/images/coming-soon-games.jpg",
            route = Screen.UlartanggaGame.route
        )
    )

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Mini Games", color = MysticGold) },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.background
                )
            )
        },
        bottomBar = { BottomNavBar(navController) },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        LazyVerticalGrid(
            columns = GridCells.Fixed(2),
            modifier = Modifier
                .padding(paddingValues)
                .fillMaxSize()
                .padding(horizontal = 8.dp),
            contentPadding = PaddingValues(vertical = 8.dp),
            verticalArrangement = Arrangement.spacedBy(4.dp),
            horizontalArrangement = Arrangement.spacedBy(4.dp)
        ) {
            items(games) { game ->
                MysticCard(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clickable { navController.navigate(game.route) }
                ) {
                    Column(
                        modifier = Modifier.padding(12.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        AsyncImage(
                            model = game.imageUrl,
                            contentDescription = game.title,
                            modifier = Modifier
                                .fillMaxWidth()
                                .aspectRatio(4f / 3f)
                                .clip(RoundedCornerShape(8.dp)),
                            contentScale = ContentScale.Crop
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = game.title,
                            color = MysticGold,
                            fontWeight = FontWeight.Bold,
                            style = MaterialTheme.typography.bodyMedium,
                            textAlign = TextAlign.Center
                        )
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(
                            text = game.description,
                            color = TextSecondary,
                            style = MaterialTheme.typography.bodySmall,
                            textAlign = TextAlign.Center
                        )
                    }
                }
            }
        }
    }
}
