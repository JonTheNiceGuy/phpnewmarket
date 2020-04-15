Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/bionic64"
  config.vm.box_check_update = false
  config.vm.network "forwarded_port", guest: 80, host: 12280
  config.vm.network "public_network", bridge: "enp0s25"
  config.vm.provision "shell", path: "install.sh"
end
