require 'csv'
require 'pry'

teams = CSV.parse(File.read("teams.csv")).flatten
score_files = Dir.glob("*score*.csv")

def compute_points(team_score, opponent_score)
  return 3 if team_score > opponent_score
  return 1 if team_score == opponent_score
  0
end

ranking = []

ranking = teams.map do |team|
  team_points = 0
  won_games = 0
  lost_games = 0
  draw_games = 0
  scored_goals = 0
  conceded_goals = 0
  diff_goals = 0
  score_files.each do |score_file|
    team_score = 0
    opponent_score = 0
    CSV.parse(File.read(score_file)).each do |row|
      next unless row[0] == team || row[1] == team
      if row[0] == team
        team_score = row[2].to_i
        opponent_score = row[3].to_i
      end
      if row[1] == team
        team_score = row[3].to_i
        opponent_score = row[2].to_i
      end
      team_points += compute_points(team_score, opponent_score)
      won_games += 1 if team_score > opponent_score
      lost_games += 1 if team_score < opponent_score
      draw_games += 1 if team_score == opponent_score
      scored_goals += team_score
      conceded_goals += opponent_score
      diff_goals += team_score - opponent_score
    end
  end
  [team, team_points, won_games, draw_games, lost_games, scored_goals, conceded_goals, diff_goals]
end

ranking = ranking.sort_by { |team| [-team[1], -team[-1]] }.each_with_index.map do |team, index|
  [index + 1, team].flatten
end
ranking.unshift(%w[rank team Pts G. N. P. p. c. Diff.])
File.write("ranking.csv", ranking.map(&:to_csv).join)
