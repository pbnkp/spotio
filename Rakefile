ROOT_DIR = File.expand_path('.')
PLUGIN_DIR = File.join(ROOT_DIR, 'Plugin')
XCODE_PROJECT = File.join(PLUGIN_DIR, 'Spotio.xcodeproj')
SPOTIO_BUNDLE = "Spotio.bundle"
PLUGIN_BUILD_DIR = File.join(PLUGIN_DIR, 'build')
PLUGIN_RELEASE_DIR = File.join(PLUGIN_BUILD_DIR, 'Release')
SIMBL_DIR = File.expand_path(File.join('~', 'Library', 'Application Support', 'SIMBL'))
SIMBL_PLUGINS_DIR = File.expand_path(File.join(SIMBL_DIR, 'Plugins'))


def colorize(text, color_code)
  "#{color_code}#{text}\e[0m"
end

def red(text); colorize(text, "\e[31m"); end
def green(text); colorize(text, "\e[32m"); end
def yellow(text); colorize(text, "\e[33m"); end
def blue(text); colorize(text, "\e[34m"); end
def magenta(text); colorize(text, "\e[35m"); end
def azure(text); colorize(text, "\e[36m"); end
def white(text); colorize(text, "\e[37m"); end
def black(text); colorize(text, "\e[30m"); end

def file_color(text); yellow(text); end
def dir_color(text); blue(text); end
def cmd_color(text); azure(text); end


def dirty_repo_warning()
  is_clean = `git status`.match(/working directory clean/)
  puts red("Repository is not clean! You should commit all changes before releasing.") unless is_clean
end


module Spotify
  def self.path
    '/Applications/Spotify.app'
  end
end


namespace :plugin do
  desc "Opens the XCode project"
  task :open do
    `open "#{XCODE_PROJECT}"`
  end

  desc "Recompile the plugin"
  task :build do
    puts "#{cmd_color('Building')} #{file_color(XCODE_PROJECT)}"
    Dir.chdir(PLUGIN_DIR) do
      system 'xcodebuild -configuration Release'
    end
  end
  
  namespace :build do
    desc "Remove the build directory"
    task :clobber do
      puts "#{cmd_color('Removing')} #{dir_color(PLUGIN_BUILD_DIR)}"
      `rm -rf #{PLUGIN_BUILD_DIR}`
    end
  end

  desc "Creates a ZIP file of the current build"
  task :release do
    puts cmd_color('Checking environment...')
    dirty_repo_warning

    mkdir_p(PLUGIN_RELEASE_DIR) unless File.exists? PLUGIN_RELEASE_DIR

    Dir.chdir(PLUGIN_RELEASE_DIR) do
      unless system('zip -r spotio.zip Spotio.bundle')
        puts red('need zip on the command line (download http://www.info-zip.org/Zip.html)')
      end
    end
  end

  desc "Installs latest release build into ~/Library/Application Support/SIMBL/Plugins"
  task :install do
    unless File.exists?(File.join(PLUGIN_RELEASE_DIR, SPOTIO_BUNDLE))
      Rake::Task["plugin:build"].execute nil
    end

    puts "#{cmd_color("Installing")} #{file_color(SPOTIO_BUNDLE)} into #{dir_color(SIMBL_PLUGINS_DIR)}"
    mkdir_p(SIMBL_PLUGINS_DIR) unless File.exists? SIMBL_PLUGINS_DIR

    Dir.chdir(PLUGIN_RELEASE_DIR) do
      `cp -R "#{SPOTIO_BUNDLE}" "#{SIMBL_PLUGINS_DIR}"`
      puts "#{blue("Done!")} #{green("Restart Spotify")}"
    end
  end

  desc "Uninstalls Spotio, you will need to restart Spotify"
  task :uninstall do
    dest = File.join(SIMBL_PLUGINS_DIR, SPOTIO_BUNDLE)
    puts "#{cmd_color("Uninstalling")} #{file_color(dest)}"
    system("rm -rf \"#{dest}\"") if File.exists? dest
    puts "#{blue("Done!")} #{red("Restart Spotify")}"
  end

  desc "Dumps Spotify into dump/Current"
  task :dump do
    `rm -rf dumps/Current`
    `mkdir dumps`
    `class-dump -I -H -o dumps/Current "/Applications/Spotify.app/Contents/MacOS/Spotify"`
  end
end


namespace :spotify do
  task :restart do
    system 'killall Spotify'
    system 'open', Spotify.path
  end
end


desc "Rebuilds the plugin and restarts Spotify"
task :update => ['plugin:build', 'spotify:restart']
