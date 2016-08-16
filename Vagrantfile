Vagrant.configure(2) do |config|
  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
  config.vm.box = "ubuntu/trusty64"
  config.vm.provision :shell, path: "provision/install.sh"
  config.vm.synced_folder ".", "/vagrant"

  config.vm.synced_folder "./var/cache", "/vagrant/var/cache",
    owner: "vagrant", group: "vagrant",
    mount_options: ["dmode=777,fmode=777"]

  config.vm.synced_folder "./var/logs", "/vagrant/var/logs",
    owner: "vagrant", group: "vagrant",
    mount_options: ["dmode=777,fmode=777"]

  config.vm.network "forwarded_port", guest: 80, host: 8051
  config.vm.network "forwarded_port", guest: 3306, host: 8052
end
