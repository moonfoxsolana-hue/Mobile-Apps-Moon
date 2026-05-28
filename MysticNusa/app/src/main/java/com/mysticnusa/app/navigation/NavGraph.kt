package com.mysticnusa.app.navigation

import androidx.compose.runtime.Composable
import androidx.navigation.NavHostController
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.navArgument
import com.mysticnusa.app.data.local.TokenManager
import com.mysticnusa.app.ui.screens.*

@Composable
fun MysticNavGraph(
    navController: NavHostController,
    tokenManager: TokenManager
) {
    NavHost(
        navController = navController,
        startDestination = Screen.Splash.route
    ) {
        composable(Screen.Splash.route) {
            SplashScreen(navController = navController, tokenManager = tokenManager)
        }
        composable(Screen.Login.route) {
            LoginScreen(navController = navController)
        }
        composable(Screen.Register.route) {
            RegisterScreen(navController = navController)
        }
        composable(Screen.Home.route) {
            HomeScreen(navController = navController)
        }
        composable(Screen.Profile.route) {
            ProfileScreen(navController = navController)
        }
        composable(Screen.TokenHistory.route) {
            TokenHistoryScreen(navController = navController)
        }
        composable(Screen.Claim.route) {
            ClaimScreen(navController = navController)
        }
        composable(Screen.News.route) {
            NewsScreen(navController = navController)
        }
        composable(
            route = Screen.NewsDetail.route,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) { backStackEntry ->
            val id = backStackEntry.arguments?.getInt("id") ?: 0
            NewsDetailScreen(navController = navController, newsId = id)
        }
        composable(Screen.Stories.route) {
            StoriesScreen(navController = navController)
        }
        composable(
            route = Screen.StoryDetail.route,
            arguments = listOf(navArgument("id") { type = NavType.IntType })
        ) { backStackEntry ->
            val id = backStackEntry.arguments?.getInt("id") ?: 0
            StoryDetailScreen(navController = navController, storyId = id)
        }
        composable(Screen.Games.route) {
            GamesScreen(navController = navController)
        }
        composable(Screen.TriviaGame.route) {
            TriviaGameScreen(navController = navController)
        }
        composable(Screen.LogicalGame.route) {
            LogicalGameScreen(navController = navController)
        }
        composable(Screen.IntuitionGame.route) {
            IntuitionGameScreen(navController = navController)
        }
        composable(Screen.TarotGame.route) {
            TarotGameScreen(navController = navController)
        }
        composable(Screen.UlartanggaGame.route) {
            UlartanggaGameScreen(navController = navController)
        }
        composable(Screen.NgepetGame.route) {
            NgepetGameScreen(navController = navController)
        }
        composable(Screen.Staking.route) {
            StakingScreen(navController = navController)
        }
    }
}
